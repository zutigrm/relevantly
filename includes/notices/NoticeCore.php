<?php
namespace Relevantly\Notices;

class NoticeCore {

    function __construct()
    {
        $this->hooks();
    }

    function hooks()
    {
        add_action( 'admin_notices', [ $this, 'admin_notices' ] );
    }

    function admin_notices()
    {
        NoticePhrases::init();
    }
}