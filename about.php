<?php
/**
 * About page: brand story and the fabric specs table.
 * Static content, so no database connection is needed.
 */
$pageTitle = 'About';
require_once __DIR__ . '/includes/header.php';

// Fabric specs per fit: name => [weight, composition, fit, finish]
$fabrics = [
    'Essentials'   => ['180 gsm', '100% combed ring-spun cotton', 'Regular', 'Pre-shrunk, enzyme washed'],
    'Oversized'    => ['260 gsm', '100% cotton, heavy jersey', 'Boxy, dropped shoulder', 'Garment-dyed'],
    'Graphic'      => ['220 gsm', '100% combed cotton', 'Relaxed', 'Water-based screen print'],
    'Vintage Wash' => ['200 gsm', '100% cotton', 'Relaxed', 'Acid or stone washed by hand'],
    'Long Sleeve'  => ['200 gsm', '95% cotton, 5% elastane cuffs', 'Regular', 'Ribbed cuffs and collar'],
];
?>

<section class="section">
    <div class="container about">
        <h1 class="page-title">We print every tee ourselves</h1>

        <div class="prose">
            <p>Store started in 2021 with a second-hand press, one screen and a garage. We printed shirts for friends' bands, then for their friends, and never stopped.</p>
            <p>Today we still pull every print by hand. Each design runs in a batch of 50 to 200 pieces. When a batch sells out we don't reprint it; we make the next one.</p>
            <p>We use water-based inks because they soak into the cotton instead of sitting on top, so prints stay soft and wear in with the shirt. Small misregistrations between colors are part of the look, not a mistake.</p>
        </div>
    </div>
</section>

<section class="section section-tint" id="fabric">
    <div class="container">
        <h2>Fabric specs</h2>
        <p class="lead">Weight is in grams per square metre (gsm). Higher means a thicker, more structured tee.</p>

        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">Fit</th>
                        <th scope="col">Weight</th>
                        <th scope="col">Fabric</th>
                        <th scope="col">Cut</th>
                        <th scope="col">Finish</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($fabrics as $fit => [$weight, $fabric, $cut, $finish]): ?>
                        <tr>
                            <th scope="row"><?= e($fit) ?></th>
                            <td class="num"><?= e($weight) ?></td>
                            <td><?= e($fabric) ?></td>
                            <td><?= e($cut) ?></td>
                            <td><?= e($finish) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <h2>Size guide</h2>
        <p class="lead">Chest width measured flat, armpit to armpit, in centimetres.</p>
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr><th scope="col">Fit</th><?php foreach (SIZES as $s): ?><th scope="col" class="num"><?= $s ?></th><?php endforeach; ?></tr>
                </thead>
                <tbody>
                    <tr><th scope="row">Regular</th><td class="num">46</td><td class="num">49</td><td class="num">52</td><td class="num">55</td><td class="num">58</td><td class="num">61</td></tr>
                    <tr><th scope="row">Oversized</th><td class="num">52</td><td class="num">55</td><td class="num">58</td><td class="num">61</td><td class="num">64</td><td class="num">67</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
