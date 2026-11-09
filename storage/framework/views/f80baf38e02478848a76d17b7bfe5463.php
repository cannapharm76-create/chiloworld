
<?php
    $brandWorld = in_array(request()->path(), \App\Http\Controllers\HomeController::BRANDS, true);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimal-ui">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', 'CHILO EXOTICS®'); ?></title>
    <meta name="description" content="<?php echo $__env->yieldContent('description'); ?>">
    <meta name="robots" content="<?php echo $__env->yieldContent('robots', 'index,follow'); ?>">
    <meta name="theme-color" content="#0d1a16">
    <meta name="apple-mobile-web-app-title" content="Chilo Exotics">
    <meta property="og:site_name" content="CHILO EXOTICS">
    <meta property="og:type" content="website">
    <meta property="og:title" content="<?php echo $__env->yieldContent('title', 'CHILO EXOTICS®'); ?>">
    <meta property="og:description" content="<?php echo $__env->yieldContent('description'); ?>">
    <meta property="og:url" content="<?php echo e(url()->current()); ?>">
    <link rel="canonical" href="<?php echo e(url()->current()); ?>">

    <link rel="icon" type="image/svg+xml" href="<?php echo e(asset('public/front/chilo/favicon.svg')); ?>">
    <link rel="apple-touch-icon" href="<?php echo e(asset('public/front/chilo/favicon.svg')); ?>">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Audiowide&family=Electrolize&family=DM+Sans:wght@400;500;600;700&family=DM+Mono:wght@400;500&family=Instrument+Serif:ital@0;1&display=swap" rel="stylesheet">

    
    <?php echo $__env->yieldPushContent('styles'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('public/front/chilo/chilo.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('public/front/chilo/chilo-theme.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('public/front/app.css')); ?>">
</head>
<body class="<?php echo e($brandWorld ? 'theme-brand' : 'theme-core'); ?>">
<div id="__next">
    <?php if (! (View::hasSection('no_header'))): ?>
        <?php echo $__env->make('front.layout.header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php endif; ?>

    <?php echo $__env->yieldContent('content'); ?>

    <?php echo $__env->make('front.chilo.footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <?php echo $__env->make('front.layout.cookie', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <?php if(session('status')): ?>
        <div class="cv-flash" role="status"><?php echo e(session('status')); ?></div>
    <?php endif; ?>
    <?php if($errors->any()): ?>
        <div class="cv-flash cv-flash--error" role="alert"><?php echo e($errors->first()); ?></div>
    <?php endif; ?>
</div>
<script>window.SITE = {root: "<?php echo e(url('/')); ?>/", assets: "<?php echo e(asset('public/front')); ?>/"};</script>
<script src="<?php echo e(asset('public/front/app.js')); ?>"></script>
<?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH D:\xampp\htdocs\Chilo\resources\views/front/layout/app.blade.php ENDPATH**/ ?>