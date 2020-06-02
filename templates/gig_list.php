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
        <th>DETAILS</th>
    </tr>
    <?php foreach ($data as $gig) : ?>
        <tr>
            <td> <?php echo $gig->gig_name; ?></td>
            <td> <?php echo date("{$date_format}", strtotime($gig->start_date)); ?></td>
            <td> <?php echo $gig->city . ', ' . $gig->region; ?></td>
            <td><a href="/ <?php echo $slug . '?gig_id=' . $gig->gig_id ?>">view</td>
        </tr>
    <?php endforeach ?>
</table>
<?php if ($archive) : ?>
    <div style="text-align: center; margin-top: 2rem;"><a href="/<?php echo $slug ?>">Display current shows</div>
<?php else : ?>
    <div style="text-align: center; margin-top: 2rem;"><a href="/<?php echo $slug ?>?archive=set">Display archived shows</div>
<?php endif ?>