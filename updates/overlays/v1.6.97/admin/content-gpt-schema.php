<?php
if (!defined('SITE_ROOT')) {
    http_response_code(404);
    exit;
}

$server = json_encode(substr($apiEndpoint, 0, -strlen('/api/v1/pages')), JSON_UNESCAPED_SLASHES);
list($shopPaths, $shopSchemas) = $gptShopAvailable ? require __DIR__ . '/content-gpt-shop-schema.php' : ['', ''];
return str_replace(['__SERVER__', '__SHOP_PATHS__', '__SHOP_SCHEMAS__'], [$server, $shopPaths, $shopSchemas], <<<'YAML'
openapi: 3.1.0
info:
  title: EWC Lite site editor
  version: '1.0'
servers:
  - url: __SERVER__
security:
  - ewcKey: []
paths:
  /api/v1/pages/search:
    get:
      operationId: searchEwcPages
      summary: Search pages by title, heading or path.
      x-openai-isConsequential: false
      parameters:
        - name: q
          in: query
          schema:
            type: string
        - name: page
          in: query
          schema:
            type: integer
            minimum: 1
      responses:
        '200':
          description: Page summaries and pagination. Use items.id as the page path.
  /api/v1/pages/page:
    get:
      operationId: readEwcPage
      summary: Read a page, its editable fields and its revision.
      x-openai-isConsequential: false
      parameters:
        - name: path
          in: query
          required: true
          schema:
            type: string
          description: Exact page id from searchEwcPages, including the .php suffix.
      responses:
        '200':
          description: Page data, HTML and current revision.
  /api/v1/pages/preview:
    post:
      operationId: previewEwcPageEdit
      summary: Validate edits and return a before-and-after preview without writing.
      x-openai-isConsequential: false
      requestBody:
        required: true
        content:
          application/json:
            schema:
              type: object
              required: [path, revision, changes]
              properties:
                path:
                  type: string
                revision:
                  type: string
                changes:
                  $ref: '#/components/schemas/PageChanges'
      responses:
        '200':
          description: Exact diff and short-lived preview token. Present the changes to the user before applying.
        '412':
          description: Page changed. Read it again and produce a fresh preview.
  /api/v1/pages/apply:
    post:
      operationId: applyEwcPageEdit
      summary: Save exactly the edits from the preview after user confirmation.
      x-openai-isConsequential: true
      requestBody:
        required: true
        content:
          application/json:
            schema:
              type: object
              required: [path, revision, changes, preview_token]
              properties:
                path:
                  type: string
                revision:
                  type: string
                changes:
                  $ref: '#/components/schemas/PageChanges'
                preview_token:
                  type: string
                  description: Exact token returned by previewEwcPageEdit for these changes.
      responses:
        '200':
          description: Edits saved. Returns the public URL and new revision.
        '412':
          description: Page or preview changed, or the preview expired. Generate a fresh preview and ask for confirmation again.
  /api/v1/pages/iblock:
    get:
      operationId: readEwcInfoBlock
      summary: Read a table referenced by a page, including columns, rows and revision.
      x-openai-isConsequential: false
      parameters:
        - name: path
          in: query
          required: true
          schema:
            type: string
        - name: id
          in: query
          required: true
          schema:
            type: string
        - name: page
          in: query
          schema:
            type: integer
            minimum: 1
      responses:
        '200':
          description: Table and revision. Rows are ordered arrays matching the columns.
  /api/v1/pages/iblock-preview:
    post:
      operationId: previewEwcInfoBlock
      summary: Preview changes to a page table without saving.
      x-openai-isConsequential: false
      requestBody:
        required: true
        content:
          application/json:
            schema:
              $ref: '#/components/schemas/InfoBlockEdit'
      responses:
        '200':
          description: Changed rows before and after, with preview token.
  /api/v1/pages/iblock-apply:
    post:
      operationId: applyEwcInfoBlock
      summary: Save the exact table changes after confirmation.
      x-openai-isConsequential: true
      requestBody:
        required: true
        content:
          application/json:
            schema:
              allOf:
                - $ref: '#/components/schemas/InfoBlockEdit'
                - type: object
                  required: [preview_token]
                  properties:
                    preview_token:
                      type: string
      responses:
        '200':
          description: Table saved with a new revision.
        '412':
          description: Table, page or preview changed. Preview again.
  /api/v1/pages/image-import:
    post:
      operationId: importEwcImages
      summary: Import up to five user-uploaded or generated images into the site's media library.
      x-openai-isConsequential: true
      requestBody:
        required: true
        content:
          application/json:
            schema:
              type: object
              required: [path, openaiFileIdRefs]
              properties:
                path:
                  type: string
                openaiFileIdRefs:
                  type: array
                  maxItems: 5
                  description: JPEG, PNG or WebP images uploaded by the user or generated in this conversation.
                  items:
                    type: string
      responses:
        '200':
          description: Imported media URLs and source paths. The page has not been changed.
__SHOP_PATHS__components:
  securitySchemes:
    ewcKey:
      type: http
      scheme: bearer
  schemas:
    PageChanges:
      type: object
      additionalProperties: false
      description: Only include fields the user asked to change. HTML is the full updated page body when included.
      properties:
        title:
          type: string
        heading:
          type: string
        description:
          type: string
        keywords:
          type: string
        html:
          type: string
    InfoBlockEdit:
      type: object
      required: [path, id, revision, mode, rows]
      properties:
        path:
          type: string
        id:
          type: string
        revision:
          type: string
        mode:
          type: string
          enum: [append, update, replace]
          description: Append preserves existing rows. Update starts at start_row. Replace overwrites all existing rows.
        start_row:
          type: integer
          minimum: 1
          description: First existing row to update, counting from 1. Required for update.
        rows:
          type: array
          maxItems: 50
          items:
            type: object
            additionalProperties:
              type: string
            description: Use exact column keys and string values returned by the table schema.
__SHOP_SCHEMAS__
YAML
);
