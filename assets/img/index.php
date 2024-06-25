<?php
$files = glob('*.{jpg,jpeg,png,gif}', GLOB_BRACE);
?>
<table>
    <tr>
        <th>Image</th>
        <th>Embed Image</th>
    </tr>
    <?php foreach ($files as $file) : ?>
        <tr>
            <td>
                <img class="rounded" src="<?php echo $file; ?>" alt="<?php echo $file; ?>" width="300">
            </td>
            <td>
                &lt;img class="rounded" src="assets/img/<?php echo $file; ?>" &gt;
            </td>
        </tr>
    <?php endforeach; ?>
</table>