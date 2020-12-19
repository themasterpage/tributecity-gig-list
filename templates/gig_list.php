<?php if (!$options['archive'] && !$options['limit'] > 0) : ?>
    <?php if ($archive) : ?>
        <h2 style="text-align: center; margin-bottom: 2rem;">Archived Show Listings</h2>
    <?php else : ?>
        <h2 style="text-align: center; margin-bottom: 2rem;">Current Show Listings</h2>
    <?php endif ?>
<?php endif ?>
<?php if (!$data || isset($data->message)) : ?>
    <div style="text-align: center; margin-bottom: 2rem;">There are no current shows to display.</div>
<?php else : ?>
    <table class="gigListTable">
        <tr>
            <th>SHOW</th>
            <th>DATE</th>
            <th>LOCATION</th>
            <?php if (!$options['archive'] && !$options['limit'] > 0) : ?>
                <th>DETAILS</th>
            <?php endif ?>
        </tr>
        <?php foreach ($data as $gig) : ?>
            <tr>
                <td> <?php echo $gig->gig_name; ?></td>
                <td> <?php echo date("{$date_format}", strtotime($gig->start_date)); ?></td>
                <td> <?php echo $gig->location . ', ' . $gig->country ?></td>
                <?php if (!$options['archive'] && !$options['limit'] > 0) : ?>
                    <td><a href="/ <?php echo $slug . '?gig_id=' . $gig->gig_id ?>">view</td>
                <?php endif ?>
            </tr>
        <?php endforeach ?>
    </table>
<?php endif ?>
<?php if (!isset($data->message) && !$options['limit']) : ?>
    <?php if ($options['archive']) : ?>
        <div style="text-align: center; margin-top: 2rem;"><a href="/<?php echo $slug ?>">Display current shows</a></div>
    <?php else : ?>
        <div style="text-align: center; margin-top: 2rem;"><a href="/<?php echo $slug ?>?archive=set">Display archived shows</a></div>
    <?php endif ?>
<?php endif ?>
<div style="text-align: right; width: 90%; font-size:x-small;">Gig list powered by <a href="https://tributecity.com">TributeCity.com</a></div>