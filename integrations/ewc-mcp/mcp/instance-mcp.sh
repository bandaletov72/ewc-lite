#!/bin/sh
set -eu
base=$(CDPATH= cd -- "$(dirname -- "$0")" && pwd)
: "${EWC_MCP_SITE_ROOT:?Set the path for this installation}"
: "${EWC_MCP_STATE_DIR:?Set a separate private draft directory}"
: "${EWC_MCP_SITE_URL:?Set the canonical URL for this installation}"
unset CONTROL_PLANE_API_KEY OPENAI_API_KEY
exec "${EWC_PHP_BINARY:-php}" "$base/server.php" "$EWC_MCP_SITE_ROOT" "$EWC_MCP_STATE_DIR" "$EWC_MCP_SITE_URL"
