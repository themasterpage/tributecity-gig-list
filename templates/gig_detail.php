<div class="gig-details-main">
    <!--Title-->
    <?php $hide = get_option('tributecity_hide_title'); ?>
    <?php if (!$hide) : ?>
        <h1 style="text-align: center; font-size: 3rem;"><?php echo $gig->band_name; ?></h1>
        <h2 style="text-align: center; font-size: 1.2rem;"><?php echo $gig->tag_line; ?></h2>
    <?php endif ?>
    <h3 style="margin: 1rem 0; font-size: 1rem; text-align: center"><?php echo $gig->gig_name . ', ' . $dateTime . ', ' .  $gig->venue_name . ', ' . $gig->city . ', ' . $gig->region; ?></h3>
    <div style="display: flex; flex-wrap: wrap; margin: .75rem 1.5rem;">
        <div class="gig-details-col1">
            <img src="<?php echo $imgServer . $gig->poster; ?>">
            <?php if (isset($gig->event_url)) : ?>
                <div class="py-1 mt-4"><a href="<?php echo $gig->event_url; ?>">Event Page</a></div>
            <?php endif; ?>
            <?php if (isset($gig->fb_event_url)) : ?>
                <div class="py-1"><a href="<?php echo $gig->fb_event_url; ?>">FaceBook Event Page</a></div>
            <?php endif; ?>
            <?php if (isset($gig->ticket_url)) : ?>
                <div class="py-1"><a href="<?php echo $gig->ticket_url; ?>">Tickets</a></div>
            <?php endif; ?>
            <div class="py-1"></div>
        </div>
        <div class="gig-details-col2">
            <h2 style="margin-bottom: .5rem;">DETAILS</h2>
            <div class="gig-details-data"><span class="gig-details-label">Date/Time:</span> <?php echo $dateTime; ?></div>
            <div class="gig-details-data"><span class="gig-details-label">Price:</span> <?php echo $gig->price; ?></div>
            <div class="gig-details-data"><span class="gig-details-label">Venue:</span> <?php echo $gig->venue_name; ?></div>
            <div class="gig-details-data"><span class="gig-details-label">Address:</span> <?php echo $gig->address_1; ?> <?php if (isset($gig->address_2)) echo $gig->address_2; ?></div>
            <div class="gig-details-data"><span class="gig-details-label">City/Town:</span> <?php echo $gig->city; ?></div>
            <div class="gig-details-data"><span class="gig-details-label">State/Region/Province:</span> <?php echo $gig->region; ?></div>
            <div class="gig-details-data"><span class="gig-details-label">Country:</span> <?php echo $gig->country; ?></div>
            <div class="gig-details-data"><span class="gig-details-label">Phone:</span> <?php echo $gig->phone; ?></div>
            <div class="gig-details-data"><span class="gig-details-label">Website:</span> <a target="_new" href="<?php echo $gig->url; ?>"><?php echo $gig->url; ?></a></div>
            <div style="width: 100%; margin-top: 1.5rem;">
                <!--  Map Data Here -->
            </div>
            <h2 style="margin-top: 1rem; margin-bottom: .5rem;">DESCRIPTION</h2>
            <div class="py-1"><?php echo $gig->description; ?></div>
        </div>
    </div>
    <h3 style="text-align: center; margin-top: 2rem; font-size: 1rem"><a href="javascript:history.go(-1)">Return to all show listings</a>
</div>