<?php
function formatDateTime(string $dateString): string {
    return date('d.m.Y H:i', strtotime($dateString));
}

function formatDate(string $dateString): string {
    return date('d.m.Y', strtotime($dateString));
}
?>