<?php if ($archive) : ?>
    <h2 style="text-align: center; margin-bottom: 2rem;">Archived Show Listings</h2>
<?php else : ?>
    <h2 style="text-align: center; margin-bottom: 2rem;">Current Show Listings</h2>
<?php endif ?>
<table class="gigListTable">
    <tr>
        <th>SHOW</th>
        <th>DATE</th>
        <th>LOCATION</th>
        <?php if (!$archive) : ?>
            <th>DETAILS</th>
        <?php endif ?>
    </tr>
    <?php foreach ($data as $gig) : ?>
        <tr>
            <td> <?php echo $gig->gig_name; ?></td>
            <td> <?php echo date("{$date_format}", strtotime($gig->start_date)); ?></td>
            <td> <?php echo $gig->city . ', ' . $gig->region . ', ' . $gig->country ?></td>
            <?php if (!$archive) : ?>
                <td><a href="/ <?php echo $slug . '?gig_id=' . $gig->gig_id ?>">view</td>
            <?php endif ?>
        </tr>
    <?php endforeach ?>
</table>
<?php if ($archive) : ?>
    <div style="text-align: center; margin-top: 2rem;"><a href="/<?php echo $slug ?>">Display current shows</div>
<?php else : ?>
    <div style="text-align: center; margin-top: 2rem;"><a href="/<?php echo $slug ?>?archive=set">Display archived shows</div>
<?php endif ?>