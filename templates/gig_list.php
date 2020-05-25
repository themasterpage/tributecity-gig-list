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