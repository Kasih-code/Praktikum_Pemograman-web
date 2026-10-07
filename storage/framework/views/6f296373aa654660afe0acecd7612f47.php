<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Form Mahasiswa</title>
</head>
<body>
    <h1>Form Mahasiswa</h1>

    <?php if($errors->any()): ?>
        <h2>Data belum valid</h2>
        <ul>
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pesan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><?php echo e($pesan); ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    <?php endif; ?>

    <form method="POST" action="/form-mahasiswa" novalidate>
        <?php echo csrf_field(); ?>
        <p>
            <label>Nama:</label><br>
            <input type="text" name="nama" value="<?php echo e(old('nama')); ?>">
        </p>
        <p>
            <label>NIM:</label><br>
            <input type="text" name="nim" value="<?php echo e(old('nim')); ?>">
        </p>
        <p>
            <label>Email:</label><br>
            <input type="email" name="email" value="<?php echo e(old('email')); ?>">
        </p>
        <p>
            <label>Usia:</label><br>
            <input type="number" name="usia" value="<?php echo e(old('usia')); ?>">
        </p>
        <button type="submit">Kirim</button>
    </form>
</body>
</html>
<?php /**PATH C:\wamp64\www\Praktikum_Pemograman-web\resources\views/form-mahasiswa.blade.php ENDPATH**/ ?>