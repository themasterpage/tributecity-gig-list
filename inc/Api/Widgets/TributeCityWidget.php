<?php

/**
 * This is a placeholder for a future implementation of the TributeCity widget.
 */

namespace Inc\Api\Widgets;

use WP_Widget;

class TributeCityWidget extends WP_Widget
{
    public $widget_ID;
    public $widget_name;
    public $widget_options = array();
    public $control_options = array();

    public function __construct()
    {
        $this->widget_ID = 'tributecity_gig_widget';
        $this->widget_name = 'TributeCity Gig Widget';
        $this->widget_options = array(
            'classname' => $this->widget_ID,
            'description' => $this->widget_name,
            'customize_selective_refresh' => true,
        );
        $this->control_options = array(
            'width' => 400,
            'height' => 350
        );
    }

    public function register()
    {
        parent::__construct($this->widget_ID, $this->widget_name, $this->widget_options, $this->control_options);

        add_action('widgets_init', array($this, 'widgetInit'));
    }

    public function widgetInit()
    {
        register_widget($this);
    }

    public function form($instance)
    {
        $bandId = !empty($instance['bandId']) ? $instance['bandId'] : esc_html__('Band ID', 'tributecity_plugin');
        $bandIdDesc = esc_attr($this->get_field_id('bandId'));
?>

        <p>
            <label for"<?php echo $bandIdDesc ?>">Band ID</label>
            <input type="text" class="widefat" id="<?php echo $bandIdDesc; ?>" name="<?php esc_attr($this->get_field_name('bandId')); ?>" value="<?php echo esc_attr($bandId); ?>">
        </p>
<?php
    }
}
