<?php

class RevistaTable{

    //private $table_name = $wpdb->prefix . 'revista';

    public function __construct()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'revista';
        $query = "CREATE TABLE IF NOT EXISTS $table_name (
            meta_id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            post_id bigint(20) NOT NULL DEFAULT '0',
            attachment_id bigint(20) NOT NULL DEFAULT '0',
            PRIMARY KEY (meta_id),
            KEY post_id (post_id),
            KEY meta_id (meta_id),
            KEY attachment_id (attachment_id))
            ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
        $wpdb->query($query);
    }

    public function insert( $post_id, $attachment_id, $meta_id = 'NULL' ){
        global $wpdb;
        $table_name = $wpdb->prefix . 'revista';
        $wpdb->insert(
            $table_name, 
            array(
                'meta_id' => $meta_id,
                'post_id' => $post_id,
                'attachment_id' => $attachment_id
            ),
            array(
                '%s',
                '%d',
                '%d'
            )
        );
    }
}