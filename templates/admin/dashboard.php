<div class="ecoursity-admin-layout">
    <h1>Dashboard Ecoursity</h1>

    <div class="ecoursity-flex">
        <?php if ($stats) :
            foreach ($stats as $key => $value) : ?>
                <div class="card">
                    <p><?php echo $value['title']; ?></p>

                    <h2><?php echo $value['value']; ?></h2>
                </div>
        <?php endforeach;
        endif; ?>
    </div>

</div>