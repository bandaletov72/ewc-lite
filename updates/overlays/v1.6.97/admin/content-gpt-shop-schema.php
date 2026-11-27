<?php
if (!defined('SITE_ROOT')) {
    http_response_code(404);
    exit;
}

$paths = <<<'YAML'
  /api/v1/shop/lookup:
    get:
      operationId: lookupEwcShop
      summary: Find products, categories or images, or read one product with its offers and photos.
      x-openai-isConsequential: false
      parameters:
        - name: kind
          in: query
          required: true
          schema:
            type: string
            enum: [products, product, categories, media]
        - name: id
          in: query
          schema:
            type: string
          description: Use a product ID from the products result when kind is product.
        - name: q
          in: query
          schema:
            type: string
        - name: folder
          in: query
          schema:
            type: string
          description: Optional directory ID returned by media lookup.
        - name: page
          in: query
          schema:
            type: integer
            minimum: 1
      responses:
        '200':
          description: Catalog revision and currency with matching records or one product.
  /api/v1/shop/preview:
    post:
      operationId: previewEwcShopChange
      summary: Validate a product or price change and return before and after without saving.
      x-openai-isConsequential: false
      requestBody:
        required: true
        content:
          application/json:
            schema:
              $ref: '#/components/schemas/ShopEdit'
      responses:
        '200':
          description: Reviewable changes and preview token.
        '412':
          description: Catalog changed. Read it again and show a new preview.
  /api/v1/shop/apply:
    post:
      operationId: applyEwcShopChange
      summary: Commit exactly the catalog change previewed and confirmed by the user.
      x-openai-isConsequential: true
      requestBody:
        required: true
        content:
          application/json:
            schema:
              allOf:
                - $ref: '#/components/schemas/ShopEdit'
                - type: object
                  required: [preview_token]
                  properties:
                    preview_token:
                      type: string
      responses:
        '200':
          description: Saved with a new catalog revision.
        '412':
          description: Catalog or proposed changes differ, or the preview expired. Preview again.
YAML;

$schemas = <<<'YAML'
    ShopEdit:
      type: object
      required: [operation, revision, data]
      properties:
        operation:
          type: string
          enum: [create_product, edit_product, reprice]
        revision:
          type: string
          description: Exact revision returned by lookupEwcShop.
        data:
          type: object
          additionalProperties: false
          description: For create_product send category_id, name, sku and price_minor. For edit_product send id and only the fields to change. For reprice send offer_ids and basis_points.
          properties:
            id:
              type: string
            category_id:
              type: string
            slug:
              type: string
            name:
              type: string
            summary:
              type: string
            description:
              type: string
            type:
              type: string
              enum: [physical, digital, service, kit]
            active:
              type: boolean
            image:
              type: string
              description: Main image ID from the media lookup. Empty string clears the main photo.
            add_photos:
              type: array
              maxItems: 50
              items:
                type: object
                required: [image, caption]
                properties:
                  image:
                    type: string
                    description: Image ID from the media lookup.
                  caption:
                    type: string
            sku:
              type: string
            price_minor:
              type: integer
              minimum: 1
              description: Price in minor units. For example, 19900 means 199.00.
            old_price_minor:
              type: integer
              minimum: 1
            prices:
              type: array
              maxItems: 25
              items:
                type: object
                required: [offer_id]
                properties:
                  offer_id:
                    type: string
                  price_minor:
                    type: integer
                    minimum: 1
                  old_price_minor:
                    type: [integer, 'null']
                    minimum: 1
                    description: An explicit null clears the old price.
            offer_ids:
              type: array
              maxItems: 25
              items:
                type: string
            basis_points:
              type: integer
              minimum: -9999
              maximum: 100000
              description: Percentage change in basis points. 1000 increases prices by 10 percent.
            keep_old_price:
              type: boolean
              description: Save the current price as the old price before recalculation.
YAML;

return [$paths . "\n", $schemas];
