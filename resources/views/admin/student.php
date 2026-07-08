<div class="ecoursity-admin-layout">
    <h1>Siswa Ecoursity</h1>

    <?php if ($students) :
        foreach ($students as $key => $value) : ?>
            <div class="card">
                <p><?php echo $value['display_name']; ?></p>

                <h2><?php echo $value['user_email']; ?></h2>
            </div>
    <?php endforeach;
    endif; ?>

</div>