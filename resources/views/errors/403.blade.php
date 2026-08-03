@include('errors.layout', [
    'code' => '403',
    'title' => 'Access Denied',
    'message' => $exception->getMessage() ?: "You don't have permission to view this page. If you think this is a mistake, contact your Super Admin.",
])
