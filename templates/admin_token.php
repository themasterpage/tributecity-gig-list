    <div class="wrap">
        <h2>TributeCity Gig List API</h2>
        <?php settings_errors(); ?>

        <ul class="nav nav-tabs">
            <li class="active"><a href="#tab-1">Manage Settings</a></li>
            <li><a href="#tab-2">About</a></li>
        </ul>

        <div class="tab-content">
            <div id="tab-1" class="tab-pane active">
                <form method="post" action="options.php">
                    <?php
                    settings_fields('tributecity_plugin_settings');
                    do_settings_sections('tributecity_plugin');
                    submit_button();
                    ?>
                </form>
            </div>
            <div id="tab-2" class="tab-pane">
                <h1>Hello There in tab 2</h1>
            </div>
        </div>


    </div>