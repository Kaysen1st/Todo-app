<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php echo html_escape($page_title); ?></title>
    <link rel="stylesheet" href="<?php echo base_url('assets/css/style.css'); ?>">
</head>
<body>
    <div class="container <?php echo isset($container_class) ? $container_class : ''; ?>">
