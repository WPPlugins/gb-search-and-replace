<?php

//Mark all the "search" words
add_action('wp_ajax_gb_replace_marker_ajax','gb_replace_marker');
function gb_replace_marker(){

    //If all required data was sent from jQuery
    if(isset($_POST['gb_post_type'])&&isset($_POST['gb_replace'])&&isset($_POST['gb_with'])&&isset($_POST['gb_tag'])&&isset($_POST['gb_replace_case'])){

        // Enables access to the database
        global $wpdb;

        //Set all ajax variables
        $gb_post_types = $_POST['gb_post_type'];
        $gb_search = $_POST['gb_replace'];
        $gb_replace = $_POST['gb_with'];
        $gb_tag = $_POST['gb_tag'];
        $gb_search_case = $_POST['gb_replace_case'];

        //Replace all special characters
        $gb_replace = gb_prepare($gb_replace);
        $gb_search = gb_prepare_q($gb_search);

        //Empty the return results
        $return_string = '';

        //If all required data is set
        if($gb_post_types != '' && $gb_search != '' && $gb_tag != '' ){

            /*
             * Prepare the sql query.
             * Searches for all instance of $gb_search(the search word) in post_content
             * In all $gb_post_types (all selected post types)
             * $gb_search_case determine the case sensitive
             */
            $sql = 'SELECT * FROM '.$wpdb->base_prefix.'posts WHERE (post_content LIKE "%'.$gb_search.'%" OR post_title LIKE  "%'.$gb_search.'%" '.($gb_search_case == 'false'?'':'COLLATE utf8_bin').') AND post_type IN (';
            $array_length = count($gb_post_types)-1;
            foreach($gb_post_types as $i => $gb_post_type){
               $sql .= '"'.$gb_post_type.'"'.(($i < $array_length)?', ':'');
            }
            $sql .= ')';

            // Get all results in array
            $result = $wpdb->get_results( $sql );
            if ($result){

                //Replace all special characters
                $gb_search_prep = gb_prepare($gb_search);

                //Loop thru all the post found with the instance of the search word
                foreach($result as $post_found){

                    $gb_html = gb_set_marker($post_found->post_content,$gb_search,$gb_search_case);
                    $gb_title_html = gb_set_marker($post_found->post_title,$gb_search,$gb_search_case);

                    $count = substr_count($gb_html[true],'gb_replace_text');
                    $count_no_code = substr_count($gb_html[false],'gb_replace_text');

                    $count += substr_count($gb_title_html[true],'gb_replace_text');
                    $count_no_code += substr_count($gb_title_html[false],'gb_replace_text');

                    //Prepare the returned HTML
                    $return_string .= '<article id="gb_post_id-'.$post_found->ID.'">';
                    $return_string .= '<h4 class="gb_post_title_con">';
                    $return_string .= '<input type="checkbox" name="gb_post_check[]" id="gb_post_check-'.$post_found->ID.'">';
                    $return_string .= '<a hreh="javascript:void(0);" class="gb_find_title">'.$gb_title_html[true].'</a> - ';
                    $return_string .= '<b class="gb_replace_count_no_code">'.$count_no_code.'</b> / <b class="gb_replace_count">'.$count.'</b>';
                    $return_string .= ' - <span>'.__('Post type',GB_REPLACE_SLUG).': '.$post_found->post_type.'</span></h4>';
                    $return_string .= '<div class="gb_find_content_con">';
                    $return_string .= '<div class="gb_button_con"><button class="button-primary gb_toggle_code"><span class="gb_toggle_on">'.__('View Code',GB_REPLACE_SLUG).'</span><span class="gb_toggle_off">'.__('View HTML',GB_REPLACE_SLUG).'</span></button></div>';
                    $return_string .= '<div class="gb_find_content">'.$gb_html[true].'</div>';
                    $return_string .= '<div class="gb_find_content_no_code">'.$gb_html[false].'</div></div>';
                    $return_string .= '</article>';
                }

            //If no instance of the search word was found, Prepare the "no result" returned HTML
            }elseif($return_string == ''){
                $return_string = '<p><h4>'.__('No results found for:',GB_REPLACE_SLUG).'</h4></p>';
                $return_string .= '<p><b>'.__('Search:',GB_REPLACE_SLUG).'</b>: '.$gb_search.'</p>';
                $return_string .= '<p><b>'.__('In:',GB_REPLACE_SLUG).'</b> '.implode(', ',$gb_post_types).'</p>';
            }

            //Return HTML
            echo $return_string;

        //If NOT all required data was set, return error 2
        }else{
            echo '2';
        }

    //If NOT all required data was sent from jQuery, return error 1
    }else{
        echo '1';
    }

    die();
}

//Return associative array of true false (with HTML tags, without HTML tags) contain the content ($text) with marked search words ($search)
function gb_set_marker($text,$search,$case_sensitive){

    //Set 2 variables with the content of the post
    //$yes_html_tags = html tags convert to html entities
    //$no_html_tags = html tags stay html
    $no_html_tags = $yes_html_tags = $text;

    //Convert special characters to HTML entities
    $text = htmlspecialchars($text);
    $search_no_tags = htmlspecialchars($search);
    //$Open_tag indicate if an html tag is open
    $open_tag = false;
    $places = '';

    //If "case sensitive" is false set all the post and replace word to lowercase
    if($case_sensitive == 'false'){
        $text = strtolower($text);
        $search = strtolower($search);
        $search_no_tags = strtolower($search_no_tags);
    }

    //Loop on all characters in the post
    for($i=0;$i<strlen($text);$i++){

        //If html tag is open
        if(substr($text,$i,4) == '&lt;'){
            //Indicate open html tag
            $open_tag = true;

        }elseif(substr($text,$i,4) == '&gt;'){
            //Indicate close html tag
            $open_tag = false;
        }

        //If index $i + the length of $search is lower or equal to the length of $text
        if($i + strlen($search) <= strlen($text)){

            //If the character $text[$i] is equal to the first letter of $search
            if($text[$i] == $search[0]){

                //If the next word in $text is equal to $search
                if(substr($text,$i,strlen($search))==$search){

                    //If the word is inside an open html tag
                    if($open_tag){
                        $places['html_code'][] = $i;
                    }else{
                        $places['no_html_code'][] = $i;
                        $places['html_code'][] = $i;

                    }
                }
            }
        //Exit the loop if there is no more room for the word $search to appear inside $text
        }else{
            break;
        }
    }

    /*
    * Start building $yes_html_tags
    */
    $search_no_tags = str_replace('\\','',$search_no_tags);
    if(!empty($places['no_html_code'])){
        //Convert special characters to HTML entities
        $yes_html_tags = htmlspecialchars($yes_html_tags);

        //Return an array with elements in reverse order
        $reverse_html_code = array_reverse($places['html_code']);

        //Loop on $reverse_html_code and insert to $yes_html_tags span tags with data in place $search appear (from the end to beginning)
        foreach($reverse_html_code as $index){
            $yes_html_tags = substr($yes_html_tags,0,$index).'<span class="gb_replace_text '.(in_array($index,$places['no_html_code'])?'gb_replace_ok':'').'" data-places="'.$index.'">'.substr($yes_html_tags,$index,strlen($search)).'</span>'.substr($yes_html_tags,$index+strlen($search),strlen($yes_html_tags));
        }
    }elseif(empty($places['html_code'])){

        //loop on all characters in the post
        for($i=0;$i<strlen($text);$i++){

            //If index $i + the length of $search is lower or equal to the length of $text
            if($i + strlen($search_no_tags) <= strlen($text)){

                //If the character $text[$i] is equal to the first letter of $search
                if($text[$i] == $search_no_tags[0]){

                    //If the next word in $text is equal to $search
                    if(substr($text,$i,strlen($search_no_tags)) == $search_no_tags){
                        $places['html_code'][] = $i;

                    }
                }
                //Exit the loop if there is no more room for the word $search to appear inside $text
            }else{
                break;
            }
        }

    }
    /*
     * End building $yes_html_tags
     * Start building $no_html_tags
     */
    if(!empty($places['no_html_code'])){
        //Return an array with elements in reverse order
        $reverse_no_html_code = array_reverse($places['no_html_code']);

        //Convert special characters to HTML entities
        $no_html_tags = htmlspecialchars($no_html_tags);

        //Loop on $reverse_no_html_code and insert to $no_html_tags span tags with data in place $search appear (from the end to beginning)
        foreach($reverse_no_html_code as $index){
            $no_html_tags = substr($no_html_tags,0,$index).'<span class="gb_replace_text gb_replace_ok" data-places="'.$index.'">'.substr($no_html_tags,$index,strlen($search_no_tags)).'</span>'.substr($no_html_tags,$index+strlen($search_no_tags),strlen($no_html_tags));
        }

        //Convert special HTML entities back to characters
        $no_html_tags = htmlspecialchars_decode($no_html_tags);
    }elseif(!empty($places['html_code'])){
        
        //Return an array with elements in reverse order
        $reverse_html_code = array_reverse($places['html_code']);

        //Convert special characters to HTML entities
        $yes_html_tags = htmlspecialchars($yes_html_tags);

        //Loop on $reverse_html_code and insert to $yes_html_tags span tags with data in place $search appear (from the end to beginning)
        foreach($reverse_html_code as $index){
            $yes_html_tags = substr($yes_html_tags,0,$index).'<span class="gb_replace_text gb_replace_ok" data-places="'.$index.'">'.substr($yes_html_tags,$index,strlen($search_no_tags)).'</span>'.substr($yes_html_tags,$index+strlen($search_no_tags),strlen($yes_html_tags));
        }


    }else{
        $places['no_html_code'] = array();
    }
    /*
     * End building $no_html_tags
     */
    $return_array[true] = $no_html_tags;
    $return_array[false] = $yes_html_tags;

    return $return_array;
}

//Replace all instance of the search word in DB
add_action('wp_ajax_gb_do_the_replace','gb_do_the_replace');
function gb_do_the_replace(){

    //If a post was selected and sent to this function
    if(isset($_POST['gb_replace_posts_id'])&&$_POST['gb_replace_posts_id']!=''){
        $gb_search_PO_id = true;
    }else{
        $gb_search_PO_id = false;
    }

    //If the selected instances were sent to this function
    if(isset($_POST['gb_replace_posts_spans']) && $_POST['gb_replace_posts_spans'] != '' ){
        $gb_search_spans = true;
    }else{
        $gb_search_spans = false;
    }

    //If the selected instances were sent to this function
    if(isset($_POST['gb_replace_title_spans']) && $_POST['gb_replace_title_spans'] != '' ){
        $gb_title_search_spans = true;
    }else{
        $gb_title_search_spans = false;
    }

    //If the 'Case Sensitive' is checked or not
    if(isset($_POST['gb_case']) && $_POST['gb_case'] != '' ){
        $gb_case = $_POST['gb_case'];
    }else{
        $gb_case = false;
    }

    //If all required data was sent from jQuery
    if($gb_search_PO_id && isset($_POST['gb_with_field']) && ($gb_search_spans || $gb_title_search_spans) && isset($_POST['gb_replace_field']) && $_POST['gb_replace_field'] != ''){

        // Enables access to database
        global $wpdb;

        //Set all ajax vars
        if($_POST['my_json'] != ''){
            $my_json = $_POST['my_json'];
        }else{
            $my_json = '';
        }


        if($_POST['my_title_json'] != ''){
            $my_title_json = $_POST['my_title_json'];
        }else{
            $my_title_json = '';
        }

        if($_POST['gb_replace_field'] != ''){
            $gb_search_field = $_POST['gb_replace_field'];
            $gb_search_field = gb_prepare($gb_search_field);

        }else{
            $gb_search_field = '';
        }

        //Check if the "Replace" filed is not empty (for deleting a word)
        if($_POST['gb_with_field'] != ''){
            $gb_replace_field = $_POST['gb_with_field'];

            //Replace all special characters
            $gb_replace_field = gb_prepare($gb_replace_field);
        }
        else
            $gb_replace_field = '';

        //Build HTML tag
        if(isset($_POST['my_tags']) && $_POST['my_tags'] != ''){
            $my_attr = $_POST['my_tags'];

            $my_element = '';

            //Set HTML element to tag
            $tag = substr($my_attr,0,strpos($my_attr,'{'));

            //Open html element
            $my_element = "<".$tag.' ';

            //Extract element properties
            $tag_attr = substr($my_attr,strpos($my_attr,'{')+1);
            $tag_attr = substr($tag_attr,0,strpos($tag_attr,'}'));

            if($tag_attr != ''){
                //Turn properties to array
                $tag_attr = explode(',',$tag_attr);
                $my_attr = array();

                //Create associative array from the properties
                foreach($tag_attr as $attr){
                    $temp_attr = explode(';',$attr);
                    $my_attr["'".$temp_attr[0]."'"]=(string)$temp_attr[1];
                }

                //Clear the apostrophes from associative array
                foreach($my_attr as $prop => $attr){
                    $my_element .= str_replace("'","",$prop).'="'.$attr.'" ';
                }
            }
            //Closing HTML element with the "Replace" field
            $my_element .= '>'.$gb_replace_field.'</'.$tag.'>';
            $gb_replace_field = $my_element;
        }

        //Set the length of the difference between the "Search" word to the "Replace" word
        $interval = strlen($gb_replace_field)-strlen($gb_search_field);

        //Replace all special characters
        $gb_search_field = gb_prepare($gb_search_field);

        //Set the JSON array for all the post id's and all the indexes of the chosen replace word
        if($my_json != '')
            $gb_json_array = gb_json($my_json);
        else
            $gb_json_array = '';

        //Set the JSON array for all the post titles id's and all the indexes of the chosen replace word in the title
        if($my_title_json != '')
            $gb_title_json_array = gb_json($my_title_json);
        else
            $gb_title_json_array = '';


        if($gb_json_array != ''){
            //Reset all variables
            $result = '';
            $content = '';
            $return_array = array();

            //Loop thru all the post id's
            foreach($gb_json_array as $i => $gb_post){

                //Get the post by id
                $the_post = get_post($i);

                //If there are more than one replacement in post($gb_post)
                if(is_array($gb_post)){

                    //Get the post content with no HTML tags
                    $content = htmlspecialchars($the_post->post_content);
                    foreach($gb_post as $x => $gb_the_post){

                        //Replace the "Search" word with the "Replace" word in post content
                        //$x * $interval = calculate the difference between the "Search" word to the "Replace" word * the place
                        $result = substr_replace($content, $gb_replace_field, $gb_the_post+($x * $interval), strlen($gb_search_field));
                        $content = $result;
                    }


                //If there is only one replacement in post($gb_post)
                }else{

                    //Get the post content with no HTML tags
                    $content = htmlspecialchars($the_post->post_content);
                    $gb_search_field = htmlspecialchars($gb_search_field);

                    //Replace the "Search" word with the "Replace" word in post content
                    $result = substr_replace($content, $gb_replace_field, $gb_post, strlen($gb_search_field));
                }

                //Loop thru all the post titles id's
                if(isset($gb_title_json_array[$i])){
                    if(is_array($gb_title_json_array[$i])){
                        $content_title = htmlspecialchars($the_post->post_title);
                        foreach($gb_title_json_array[$i] as $x => $gb_the_post){

                            //Replace the "Search" word with the "Replace" word in post content
                            //$x * $interval = calculate the difference between the "Search" word to the "Replace" word * the place
                            $result_title = substr_replace($content_title, $gb_replace_field, $gb_the_post+($x * $interval), strlen($gb_search_field));
                            $content_title = $result_title;
                        }
                    }else{
                        //Get the post content with no HTML tags
                        $content_title = htmlspecialchars($the_post->post_title);
                        $gb_search_field = htmlspecialchars($gb_search_field);
                        //Replace the "Search" word with the "Replace" word in post content
                        $result_title = substr_replace($content_title, $gb_replace_field, $gb_title_json_array[$i], strlen($gb_search_field));
                    }
                }

                //Return the results to HTML mode
                $result = htmlspecialchars_decode($result);

                //Return the results title to HTML mode
                if(isset($result_title)){
                    $result_title = htmlspecialchars_decode($result_title);
                }else{
                    $result_title = $the_post->post_title;
                }

                /*
                 * Update the post with the new results
                 */
                $update_post = array(
                    'ID'            => $the_post->ID,
                    'post_content'  => $result,
                    'post_title'  => $result_title
                );
                wp_update_post( $update_post );


                $the_post = get_post($i);
                $result_array = gb_set_marker($the_post->post_content,$gb_replace_field,$gb_case);

                //Build array of HTML to return
                $return_array[$i] = '<h4 class="gb_replace_post_title"><a href="javascript:void(0);">'.$the_post->post_title.'</a></h4>';
                $return_array[$i] .= '<div class="gb_replace_content"><a href="'.$the_post->guid.'" class="button-primary" target="_blank">'.__('View Post',GB_REPLACE_SLUG).'</a><p class="gb_replace_view_post">'.$result_array[true].'</p>';
            }
        }elseif($gb_title_json_array != ''){
            foreach($gb_title_json_array as $i => $gb_post){

                //Get the post by id
                $the_post = get_post($i);

                //If there are more than one replacement in post($gb_post)
                if(is_array($gb_post)){

                    //Get the post content with no HTML tags
                    $content_title = htmlspecialchars($the_post->post_title);
                    foreach($gb_post as $x => $gb_the_post){

                        //Replace the "Search" word with the "Replace" word in post content
                        //$x * $interval = calculate the difference between the "Search" word to the "Replace" word * the place
                        $result_title = substr_replace($content_title, $gb_replace_field, $gb_the_post+($x * $interval), strlen($gb_search_field));
                        $content_title = $result_title;
                    }


                    //If there is only one replacement in post($gb_post)
                }else{

                    //Get the post content with no HTML tags
                    $content_title = htmlspecialchars($the_post->post_title);
                    $gb_search_field = htmlspecialchars($gb_search_field);

                    //Replace the "Search" word with the "Replace" word in post content
                    $result_title = substr_replace($content_title, $gb_replace_field, $gb_post, strlen($gb_search_field));
                }

                //Return the results to HTML mode
                if(isset($result)){
                    $result = htmlspecialchars_decode($result);
                }else{
                    $result = $the_post->post_content;
                }


                //Return the results title to HTML mode
                if(isset($result_title)){
                    $result_title = htmlspecialchars_decode($result_title);
                }else{
                    $result_title = $the_post->post_title;
                }

                /*
                 * Update the post with the new results
                 */
                $update_post = array(
                    'ID'            => $the_post->ID,
                    'post_content'  => $result,
                    'post_title'  => $result_title
                );
                wp_update_post( $update_post );


                $the_post = get_post($i);
                $result_array = gb_set_marker($the_post->post_content,$gb_replace_field,$gb_case);
                $result_title_array = gb_set_marker($the_post->post_title,$gb_replace_field,$gb_case);

                //Build array of HTML to return
                $return_array[$i] = '<h4 class="gb_replace_post_title"><a href="javascript:void(0);">'.$result_title_array[true].'</a></h4>';
                $return_array[$i] .= '<div class="gb_replace_content"><a href="'.$the_post->guid.'" class="button-primary" target="_blank">'.__('View Post',GB_REPLACE_SLUG).'</a><p class="gb_replace_view_post">'.$result_array[true].'</p>';
            }
        }

        /*
         * Prepare the return HTML from the array $return_array
         */
        $return_html = '';
        $gb_replace_field = gb_prepare_q($gb_replace_field);
        foreach($return_array as $return_post){

            //Return the tagged content
            $return_post_tag = gb_tag_word($gb_replace_field,'gb_replace_after_text',$return_post);
            $return_html .= '<article>'.htmlspecialchars_decode($return_post_tag).'</article>';
        }

        //Return the HTML
        echo $return_html;

    //If NOT all required data was sent from jQuery return error 1
    }else{
        echo '1';
    }

    die();
}

//GB JSON function return associative array of posts id and index to replace
function gb_json($my_json){
    $return_json = array();
    foreach($my_json as $i => $json){
        if(strpos($json,',')>0)
            $return_json[$i] = explode(',',$json);
        else
            $return_json[$i] = $json;
    }
    return $return_json;
}

//Tag the replace word in the content and return the tagged content
function gb_tag_word($tag,$class,$text_filed){
    return (str_replace($tag,"<span class='".$class."'>".$tag."</span>",$text_filed));
}

//Prepare the string for searching substring with no quotes or apostrophe
function gb_prepare($need_preparation){
    $special_char = array(
        "\'",
        '\"'
    );
    $char = array(
        "'",
        '"'
    );
    return(str_replace($special_char,$char,$need_preparation));
}

//Revert the &quot; back to quotes
function gb_prepare_q($need_preparation){
    $special_char = array(
        '"'
    );
    $char = array(
        '&quot;'
    );
    return(str_replace($char,$special_char,$need_preparation));
}

//Subscribe to GB-Plugins
add_action('wp_ajax_gb_subscribe','gb_subscribe');
function gb_subscribe(){
    if(isset($_POST['data']) && isset($_POST['email'])){
        if(filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
            //cURL thanks to http://www.html-form-guide.com/php-form/php-form-submit.html
            //create cURL connection
            $curl_connection = curl_init('http://gb-plugins.com/#wysija');

            //set options
            curl_setopt($curl_connection, CURLOPT_CONNECTTIMEOUT, 30);
            curl_setopt($curl_connection, CURLOPT_USERAGENT,
                "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)");
            curl_setopt($curl_connection, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl_connection, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl_connection, CURLOPT_FOLLOWLOCATION, 1);

            //set data to be posted
            curl_setopt($curl_connection, CURLOPT_POSTFIELDS, $_POST['data']);

            //perform our request
            $result = curl_exec($curl_connection);

            //close the connection
            curl_close($curl_connection);

            $gb_subscribe_result = '';
            $gb_subscribe_result =  __('Thank you for subscribing to our newsletter. A verification email was sent to your e mail: ',GB_REPLACE_SLUG).$_POST['email'];

            update_option( 'gb_newsletter',true );
            echo $gb_subscribe_result;
        }else{
            echo 2;
        }
    }else{
        echo 1;
    }
    die(1);
}

//Ajax to show/hide the donation box
add_action('wp_ajax_gb_donation','gb_donation');
function gb_donation(){
    if(isset($_POST['data'])){
        $gb_donation = get_option('gb_donation');
        $return = '';
        if($gb_donation != '1'){
            update_option('gb_donation','1');
            $return = __('Thanks you for helping us',GB_REPLACE_SLUG);
        }else{
            update_option('gb_donation','0');
            $return = __("If you appreciate the kindness shown you by others, say it with deeds as well as words.",GB_REPLACE_SLUG);
        }
        echo $return;
    }
    die();
}

?>