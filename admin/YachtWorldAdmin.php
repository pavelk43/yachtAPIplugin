<?php
class YachtWorldAdmin
{

    // private $YWAdmin = new WPYachtWorldAdmin();
    private $plugin_name = "my-setting-yacht";
    private $options;
    private $id_number;
    private $id_number_2;
    private $page_size;

    public function GetPageSize(){
        $options = get_option($this->plugin_name);
        $this->page_size = $options['page_size'];
        return $this->page_size;
    }

    public function __construct()
    {
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'page_init' ) );
    }

    public function print_section_info()
    {
        print 'Enter your settings below:';
    }
    public function add_plugin_page()
    {
        add_options_page(
            'Settings Admin',
            'Yachtworld Settings',
            'manage_options',
            'my-setting-yacht',
            array( $this, 'create_admin_page' )
        );
    }

    public function create_admin_page()
    {
        $options = get_option($this->plugin_name);
        $this->id_number = $options['id_number'];
        $this->id_number_2 = $options['id_number_2'];

        $this->page_size = $options['page_size']
        ?>

        <div class="wrap">
            <h1>Yachtworld Settings</h1>
            <form method="post" action="options.php">
                <?php
                // This prints out all hidden setting fields
                settings_fields($this->plugin_name);
                do_settings_sections($this->plugin_name);
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    public function page_init()
    {
        register_setting(
            $this->plugin_name, $this->plugin_name,
            array( $this, 'sanitize' )
        );

        add_settings_section(
            'setting_section_id',
            'Update Yachtworld API Settings',
            array( $this, 'print_section_info' ),
            'my-setting-yacht'
        );

        add_settings_field(
            'id_number',
            'API Key',
            array( $this, 'id_number_callback' ),
            'my-setting-yacht',
            'setting_section_id'
        );

        add_settings_field(
            'id_number_2',
            'Co-Brokerage API Key',
            array( $this, 'id_number_2_callback' ),
            'my-setting-yacht',
            'setting_section_id'
        );


        add_settings_field(
            'page_size',
            'Page Size',
            array( $this, 'page_size_callback' ),
            'my-setting-yacht',
            'setting_section_id'
        );
    }

    public function sanitize( $input )
    {
        $new_input = array();
        if( isset( $input['id_number'] ) )
            $new_input['id_number'] = ( $input['id_number'] );

        if( isset( $input['id_number_2'] ) )
            $new_input['id_number_2'] = ( $input['id_number_2'] );

        if( isset( $input['page_size'] ) )
            $new_input['page_size'] = ( $input['page_size'] );

        return $new_input;
    }



    public function id_number_callback()
    {
        printf(
            '<input type="text" style="width:780px;" id="id_number" name="'. $this->plugin_name .'[id_number]" value="%s" />',
            $this->id_number
        );
    }

    public function id_number_2_callback()
    {
        printf(
            '<input type="text" style="width:780px;" id="id_number_2" name="'. $this->plugin_name .'[id_number_2]" value="%s" />',
            $this->id_number_2
        );
    }


    public function page_size_callback()
    {
        printf(
            '<input type="text" style="width:780px;" id="page_size" name="'. $this->plugin_name .'[page_size]" value="%s" />',
            $this->page_size
        );
    }





}

