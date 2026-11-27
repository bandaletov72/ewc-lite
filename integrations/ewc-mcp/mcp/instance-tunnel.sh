#!/bin/sh
set -eu
base=$(CDPATH= cd -- "$(dirname -- "$0")" && pwd)
case "$base" in
  *[!a-zA-Z0-9_./-]*) echo 'Use an absolute directory without whitespace or special characters.' >&2; exit 2 ;;
esac
: "${EWC_MCP_SITE_ROOT:?Set the path for this installation}"
: "${EWC_MCP_STATE_DIR:?Set a separate private draft directory}"
: "${EWC_MCP_SITE_URL:?Set the canonical URL for this installation}"
: "${CONTROL_PLANE_TUNNEL_ID:?Set a unique OpenAI tunnel ID for this installation}"
: "${CONTROL_PLANE_API_KEY:?Inject this installation’s restricted key from the host’s secret store}"
exec "${EWC_TUNNEL_BINARY:-tunnel-client}" run \
    --control-plane.tunnel-id "$CONTROL_PLANE_TUNNEL_ID" \
    --control-plane.api-key env:CONTROL_PLANE_API_KEY \
    --mcp.command "command=$base/instance-mcp.sh,channel=main" \
    --health.listen-addr 127.0.0.1:0
