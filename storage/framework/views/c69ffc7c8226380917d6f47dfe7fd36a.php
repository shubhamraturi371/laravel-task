<!-- emails/campaign.blade.php -->
<?php $__env->startComponent('mail::message'); ?>
    # Hello <?php echo e($data->name); ?>


    We would like to introduce you to <?php echo e($data->subject); ?>.

    Thanks,<br>
    <?php echo e(config('app.name')); ?>

<?php echo $__env->renderComponent(); ?>
<?php /**PATH /home/scottshubham/Documents/projects/laravelapp/resources/views/emails/components/MailHeader.blade.php ENDPATH**/ ?>