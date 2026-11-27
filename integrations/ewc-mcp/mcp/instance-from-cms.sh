#!/bin/sh
set -eu
base=$(CDPATH= cd -- "$(dirname -- "$0")" && pwd)
: "${EWC_MCP_SITE_ROOT:?Set EWC_MCP_SITE_ROOT to this installation}"
: "${CONTROL_PLANE_API_KEY:?Inject the tunnel key from your service's secret store}"
settings=$("${EWC_PHP_BINARY:-php}" "$base/instance-config.php" "$EWC_MCP_SITE_ROOT")
IFS=$(printf '\t') read -r CONTROL_PLANE_TUNNEL_ID EWC_MCP_SITE_URL <<EOF
$settings
EOF
export CONTROL_PLANE_TUNNEL_ID EWC_MCP_SITE_URL
exec "$base/instance-tunnel.sh"
