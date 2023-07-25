<?php

class ProcessCSV
{
    private $ids = array();
    private $urls = array();
    private $table;

    public function __construct(){
        // 'csv_file' é o name do input file
        if( isset( $_FILES['csv_file']) && $_FILES['csv_file']['error'] == 0 ){
            $csv_file = $_FILES['csv_file']['tmp_name'];
            // envia o arquivo para o método processFile
            // este arquivo vai ficar sendo passado de método em método por parâmetro
            $result = $this->processFile( $csv_file );
            return $result;
        }
    }

    // Este método faz o meio de campo entre o upload e o processamento do arquivo
    private function processFile( $csv_file ){
        $result = $this->get_csv( $csv_file );
        $this->create_images( $csv_file );
        return $result;
    }

    private function get_csv( $csv_file ){
        $file_to_read = fopen( $csv_file, 'r' );
        while ( !feof( $file_to_read ) ) {
            $lines[] = fgetcsv( $file_to_read, 0, ',' );
        }
        fclose( $file_to_read );

        $result = array();

        foreach( $lines as $line ){
            $result[] = [
                'id'    => $line[0],
                'url'   => $line[1]
            ];
        }

        return $result;
    }

    private function create_images( $csv_file ){
        $source = $this->get_csv( $csv_file );

        for ( $i=0; $i < count($source); $i++) { 
            $this->ids[] = (int) $source[$i]['id']; 
            $this->urls[] = $source[$i]['url']; 
        }

        $path = wp_upload_dir();
        $imgpath = $path['path'];
        $image_url = $path['url'];
        $image_subdir = ltrim( $path['subdir'], '/' );

        foreach( $this->urls as $i => $url ){
            $content = file_get_contents( $url );
            wp_upload_bits( 'post-' . $this->ids[$i] . '.jpg', null, $content );

            $post_title = get_post_field( 'post_title', $this->ids[$i] );

            $attachment_id = wp_insert_attachment( array(
                'post_title' => $post_title,
                'post_content' => '',
                'post_status' => 'inherit',
                'post_mime_type' => 'image/jpeg',
                'post_type' => 'attachment',
                'post_parent' => $this->ids[$i],
                'guid' => $image_url . '/post-' . $this->ids[$i] . '.jpg'
            ));

            if ( ! is_wp_error( $attachment_id ) ) {
                $attach_data = wp_generate_attachment_metadata( $attachment_id, $imgpath . '/post-' . $this->ids[$i] . '.jpg' );
                wp_update_attachment_metadata( $attachment_id, $attach_data );
                update_post_meta( $attachment_id, '_wp_attached_file', $image_subdir . '/post-' . $this->ids[$i] . '.jpg');
                update_post_meta( $this->ids[$i], '_thumbnail_id', $attachment_id);
                //set_post_thumbnail( absint($this->ids[$i]), absint($attachment_id) );
            }

            // Insere os metadados no banco de dados
            $this->table = new RevistaTable();
            $this->table->insert( $this->ids[$i], $attachment_id, 'NULL' );

        }

        
    }

}