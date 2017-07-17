jQuery(document).ready(function(){
    gb_do_first();
    jQuery('#gb_replace_btn-view').on('click',check_fields);
    jQuery('#gb_replace_btn').on('click',gb_replace);
    jQuery(document).on('click','.gb_find_title',function(){
        jQuery(this).closest('article').find('.gb_find_content_con').toggle();
    });
    jQuery(document).on('click','.gb_toggle_code',function(){gb_open_content(this)});
    jQuery(document).on('click','.gb_check_all',gb_check_all);
    jQuery(document).on('click','.gb_replace_text',function(){gb_close_span(this)});
    jQuery(document).on('click','input[id^="gb_post_check"]',checkbox_click);
    jQuery(document).on('click','.gb_find_content a',function(event){event.preventDefault();});
    jQuery(document).on('click','.gb_replace_post_title',function(){jQuery(this).parent().find('.gb_replace_content').toggle();});
    jQuery(document).on('change','#gb_replace_tags',function(){jQuery(this).parent().find('.gb_replace_warning').toggle();});
    jQuery(document).on('change','.gb_with_field_element',show_attr);
    jQuery('#gb_add_to_tag input').blur(function(){gb_check_attr(jQuery(this));});
    jQuery('#gb_clear_attr').click(function(){clearAllInputs(jQuery(this).closest('ul'))});
    jQuery('#gb_save_attr').click(close_attr);
    jQuery('.gb_close_open').click(close_and_clear_attr);
    jQuery('#form-wysija-2').submit(gb_subscribe);
    jQuery('#gb_donated_con #gb_donated').change(function(){gb_donation(jQuery(this))});
    jQuery('.gb_for_media').click(function(){jQuery(this).parent().toggleClass('gb_open');});

});

//Do on document loaded
function gb_do_first(){

    if(jQuery('.gb_fallow_us').length > 0){
        set_gb_fallow_us();
    }
}

//validate the field's
function check_fields(){
    var post_type = [];
    var gb_replace = '';
    var gb_with = '';
    var gb_tag = '';
    var gb_message = [];
    if(jQuery(".gb_in_field input[name='gb_replace_in[]']:checked").length){
        jQuery(".gb_in_field input[name='gb_replace_in[]']:checked").each(function ()
        {
            post_type.push(jQuery(this).val());
        });
    }else{
        gb_message.push('Please select where you want to search');
    }
    if(jQuery('#gb_replace_field').val() != ''){
        gb_replace = jQuery('#gb_replace_field').val();
    }else{
        gb_message.push('Please insert a word/sentence/HTML tag to the search field');
    }

    if(jQuery('.gb_with_field_con option:selected').length){
        gb_tag = jQuery('.gb_with_field_con option:selected').val();
    }else{
        gb_message.push('An error occurred, please try again');
    }
    if(gb_message.length > 0){
        gb_message_show(gb_message, 'gb_error');
        jQuery('.gb_replace_details_con').css('display','none');
    }else{
        jQuery('#gb_replace_message').css('display','none');
        jQuery('.gb_replace_details_con').html('');
        add_to_details(post_type,gb_replace,gb_with,gb_tag,jQuery('#gb_replace_case').prop('checked'));
    }
}

//validate fields
function gb_validate(field,gb_case){
    var valid = true;
    if(field.attr('class') != 'free_text'){
        if(field.val().indexOf('"') < 0  && field.val().indexOf("'") < 0){
            switch (gb_case.toLowerCase()){
                case('id'):
                    valid = (/^[a-zA-Z0-9-_]+$/).test(field.val())
                    break;
                case('data-type'):
                    valid = (/^[a-zA-Z0-9-_]+$/).test(field.val())
                    break;
                case('class'):
                    valid = (/^[a-zA-Z0-9-_]+$/).test(field.val())
                    break;
                case('href'):
                    valid = (/^[\S]*$/).test(field.val())
                    break;
                default:
                    valid = true;
            }
        }else{
            valid = false;
        }
    }
    return valid;
}

//check_tag_fields
function check_tag_fields(){
    var error_attr = new Array();
    jQuery('#gb_add_to_tag input[name^="add"]').each(function(){
        if(jQuery(this).val()!=''){
            var attr_type = jQuery(this).attr('name');
            attr_type = attr_type.substr(3);
            if(!gb_validate(jQuery(this),attr_type))
                error_attr.push('The '+attr_type+' field has invalid value.' );
        }
    });
    if(error_attr.length > 0){
        gb_message_show(error_attr,'gb_error',false);
        return false;
    }else{
        return true;
    }
}

//Show the plugin messages
function gb_message_show(gb_message,message_type,hide){
    if (hide === undefined) hide = true;
    if(hide)
        jQuery('#gb_replace_message').html('');
    if(Object.prototype.toString.call(gb_message) === '[object Array]'){
        for(var i in gb_message) {
            jQuery('#gb_replace_message').append("<p class='"+message_type+"'>"+gb_message[i]+"</p>");
        }
    }else{
        jQuery('#gb_replace_message').append("<p class='"+message_type+"'>"+gb_message+"</p>");
    }

    jQuery('#gb_replace_message').addClass('DIB');
}

//Ajax the search terms
function add_to_details(post_type,gb_replace,gb_with,gb_tag,gb_case){
    jQuery('#gb_replace_btn-view').attr('disabled','disabled');
    data = {
        action: 'gb_replace_marker_ajax',
        gb_post_type    : post_type,
        gb_replace      : gb_replace,
        gb_with         : gb_with,
        gb_tag          : gb_tag,
        gb_replace_case : gb_case
    };
    jQuery.post(ajaxurl, data, function (response){
        switch (response){
            case '1':
                gb_message_show('Error not all the data was transferred, please try again','gb_error');
                break;
            case '2':
                gb_message_show('Error the "search" field is empty','gb_error');
                break;
            default:
                jQuery('#gb_replace_message').html('<p>'+response+'</p>');
                //set_count();
                jQuery('#gb_replace_message').css('display','block');
                jQuery('#gb_replace_done').css('display','none');
                jQuery('#gb_replace_btn').css('display','inline-block');
                jQuery('#gb_replace_btn').attr('disabled','disabled');
                break;
        }
        jQuery('#gb_replace_btn-view').removeAttr('disabled');
    });

}

//Open and close .gb_find_content_con
function gb_open_content(item){
    if(jQuery(item).closest('.gb_find_content_con').find('.gb_find_content').is(':visible')){
        jQuery(item).closest('.gb_find_content_con').find('.gb_find_content').css('display','none');
        jQuery(item).closest('.gb_find_content_con').find('.gb_find_content_no_code').css('display','block');
        jQuery(item).find('span.gb_toggle_on').css('display','none');
        jQuery(item).find('span.gb_toggle_off').css('display','block');
    }else{
        jQuery(item).closest('.gb_find_content_con').find('.gb_find_content').css('display','block');
        jQuery(item).closest('.gb_find_content_con').find('.gb_find_content_no_code').css('display','none');
        jQuery(item).find('span.gb_toggle_on').css('display','block');
        jQuery(item).find('span.gb_toggle_off').css('display','none');
    }
}

//Set count ,coloring and value in span .gb_find_content and .gb_find_content_no_code
function set_count(){
    var to_replace = 'class="gb_replace_text"';
    jQuery('article[id^="gb_post_id"]').each(function(){
        var post_id = jQuery(this).attr('id');

        //Yes code
        var temp_text = jQuery(this).find('.gb_find_content').html();
        var index_of = temp_text.indexOf(to_replace,index_of);
        while(temp_text.indexOf(to_replace,index_of)>=0){
            temp_text = temp_text.substr(0,temp_text.indexOf(to_replace,index_of)+to_replace.length)+" value='post_id_in_"+index_of+'-'+post_id+"'"+temp_text.substr(temp_text.indexOf(to_replace,index_of)+to_replace.length,temp_text.length);
            index_of = temp_text.indexOf(to_replace,index_of)+1;
        }
        
        //No code
        var temp_text_no = jQuery(this).find('.gb_find_content_no_code').html();
        var index_of_no = temp_text_no.indexOf(to_replace,index_of_no);
        while(temp_text_no.indexOf(to_replace,index_of_no)>=0){
            temp_text_no = temp_text_no.substr(0,temp_text_no.indexOf(to_replace,index_of_no)+to_replace.length)+" value='post_id_in_"+index_of_no+'-'+post_id+"'"+temp_text_no.substr(temp_text_no.indexOf(to_replace,index_of_no)+to_replace.length,temp_text_no.length);
            index_of_no = temp_text_no.indexOf(to_replace,index_of_no)+1;
        }
        jQuery(this).find('.gb_find_content_no_code').html(temp_text_no);

        temp_text = temp_text.replace(/&lt;/g, '<');
        temp_text = temp_text.replace(/&gt;/g, '>');
    });

}

//Remove the clicked replace span
function gb_close_span(the_span){
    var gb_val = jQuery(the_span).html();
    var gb_count = 0;
    var gb_parent = jQuery(the_span).closest('div[class^="gb_find_content"]');
    var gb_parent_con = jQuery(the_span).closest('article[id^="gb_post_id"]');
    if(!jQuery(the_span).closest('h4').hasClass('.gb_post_title_con')){
        if(gb_parent.attr('class') == 'gb_find_content_no_code'){
            if(gb_parent_con.find('.gb_find_content').find('span[data-places="'+jQuery(the_span).attr('data-places')+'"]').length){
                gb_parent_con.find('.gb_find_content').find('span[data-places="'+jQuery(the_span).attr('data-places')+'"]').replaceWith(gb_val);
                gb_count = gb_parent_con.find('.gb_replace_count').html();
                gb_count = parseInt(gb_count)-1;
                gb_parent_con.find('.gb_replace_count').html(gb_count);
            }
            gb_count = gb_parent_con.find('.gb_replace_count_no_code').html();
            gb_count = parseInt(gb_count)-1;
            gb_parent_con.find('.gb_replace_count_no_code').html(gb_count);
        }else{
            gb_parent_con.find('.gb_find_content_no_code').find('span[data-places="'+jQuery(the_span).attr('data-places')+'"]').replaceWith(gb_val);
            gb_count = gb_parent_con.find('.gb_replace_count').html();
            gb_count = parseInt(gb_count)-1;
            gb_parent_con.find('.gb_replace_count').html(gb_count);
            gb_count = gb_parent_con.find('.gb_replace_count_no_code').html();
            gb_count = parseInt(gb_count)-1;
            gb_parent_con.find('.gb_replace_count_no_code').html(gb_count);
        }
    }else{
        gb_parent_con.find('.gb_post_title_con').find('span[data-places="'+jQuery(the_span).attr('data-places')+'"]').replaceWith(gb_val);
        gb_count = gb_parent_con.find('.gb_replace_count').html();
        gb_count = parseInt(gb_count)-1;
        gb_parent_con.find('.gb_replace_count').html(gb_count);
    }
    gb_parent_con.find('span[data-places="'+jQuery(the_span).attr('data-places')+'"]').replaceWith(gb_val);

}

//Enable/Disable the replace button
function checkbox_click(){
    if(jQuery( 'input[id^="gb_post_check"]:checked' ).length){
        jQuery('#gb_replace_btn').removeAttr('disabled');
    }else{
        jQuery('#gb_replace_btn').attr('disabled','disabled');
    }
}

//Check if post valid to replace
function gb_replace(){
    if(jQuery( 'input[id^="gb_post_check"]:checked' ).length){
        var gb_not_approve = [];
        jQuery('input[id^="gb_post_check"]:checked').each(function(){
            var gb_parent = jQuery(this).closest('article[id^="gb_post_id"]');
            if(parseInt(jQuery(gb_parent).find('.gb_replace_count_no_code').html())>parseInt(jQuery(gb_parent).find('.gb_replace_count').html())){
                gb_not_approve.push(gb_parent.find('.gb_post_title_con').html());
            }
        });
        if(gb_not_approve.length>0){
            var gb_dialog = jQuery('.gb_helper').find('.gb_the_dialog').clone();
            var dialog_content = '';
            jQuery.each(gb_not_approve,function(index, item){
                dialog_content += '<li>'+item+'</li>';
            });


            gb_dialog.find('.gb_dialog_list').html(dialog_content);
            gb_dialog.dialog({
                width: 'auto',
                title : gb_dialog.attr('data-title'),
                'dialogClass' : 'wp-dialog gb_style',
                modal: true,
                resizable: false,
                buttons: [{
                    text : gb_dialog.attr('data-ok'),
                    class : 'gb_button-red',
                    click : function() {
                        jQuery(this).dialog("close");
                        gb_do_the_replace();
                    }
                },{
                    text : gb_dialog.attr('data-no'),
                    class : 'gb_button-green',
                    click : function() {
                        jQuery(this).dialog("close");
                    }
                }]
            });
        }else{
            gb_do_the_replace();
        }
    }else{
        gb_message_show('Please select where you want to search', 'gb_error');
    }
}

//Ajax do the replace
function gb_do_the_replace(){
    var gb_replace_posts_id = [];
    var gb_replace_posts_spans = '';
    var gb_replace_title_spans = '';
    var my_json_init = [];
    var my_title_json_init = [];
    var gb_attr = '';
    jQuery('input[id^="gb_post_check"]:checked').each(function(){
        var gb_parent = jQuery(this).closest('article[id^="gb_post_id"]').attr('id');
        gb_replace_posts_id.push(gb_parent.substr(gb_parent.indexOf('-')+1));
    });
    jQuery.each(gb_replace_posts_id,function(index,value){
        if(jQuery('article[id$="-'+value+'"]').find('.gb_find_content_no_code').find('.gb_replace_text').length){
            gb_replace_posts_spans = '';
            jQuery('article[id$="-'+value+'"]').find('.gb_find_content_no_code').find('.gb_replace_text').each(function(){
                gb_replace_posts_spans += jQuery(this).attr('data-places')+',';
            });
            my_json_init.push('"'+value+'":"'+gb_replace_posts_spans.substring(0,gb_replace_posts_spans.length-1)+'"');
        }

        if(jQuery('article[id$="-'+value+'"]').find('.gb_post_title_con').find('.gb_replace_text').length){
            gb_replace_title_spans = '';
            jQuery('article[id$="-'+value+'"]').find('.gb_post_title_con').find('.gb_replace_text').each(function(){
                gb_replace_title_spans += jQuery(this).attr('data-places')+',';
            });
            my_title_json_init.push('"'+value+'":"'+gb_replace_title_spans.substring(0,gb_replace_title_spans.length-1)+'"');
        }
    });

    if(jQuery('.gb_with_field_element option:selected').val() != 'null'){
        if(!check_tag_fields()){
            return;
        }
        gb_attr = jQuery('.gb_with_field_element option:selected').val();
        gb_attr += "{";
        jQuery('#gb_add_to_tag input[name^="add"]').each(function(){
            if(jQuery(this).val() != "" && !jQuery(this).hasClass('input_not_clear')){
                var attr_name = jQuery(this).attr('name');

                attr_name = attr_name.substring(3,attr_name.length).toLowerCase();
                if(attr_name != 'data-type'){
                    gb_attr += attr_name + ';' +jQuery(this).val()+',';
                }else{
                    gb_attr += 'data-' +jQuery(this).val()+';'+jQuery('#TypeVal').val()+',';
                }
            }
        });
        if(gb_attr.lastIndexOf(',')>0)
            gb_attr = gb_attr.substring(0,gb_attr.lastIndexOf(','));
        gb_attr += "}";
    }

    if(gb_replace_posts_id.length > 0){
        if(my_json_init != ''){
            var my_json = jQuery.parseJSON( '{'+my_json_init+'}' );
        }else{
            var my_json = '';
        }
        if(my_title_json_init != ''){
            var my_title_json = jQuery.parseJSON( '{'+my_title_json_init+'}' );
        }else{
            var my_title_json = '';
        }

        data = {
            action: 'gb_do_the_replace',
            gb_replace_posts_id     : gb_replace_posts_id,
            gb_replace_field        : jQuery('#gb_replace_field').val(),
            gb_with_field           : jQuery('#gb_with_field').val(),
            gb_replace_posts_spans  : gb_replace_posts_spans,
            gb_replace_title_spans  : gb_replace_title_spans,
            my_json                 : my_json,
            my_title_json                 : my_title_json,
            my_tags                 : gb_attr,
            gb_case                 : jQuery('#gb_replace_case').prop('checked')
        };
        jQuery.post(ajaxurl, data, function (response){
            switch (response){
                case '1':
                    gb_message_show('Error not all the data was transferred, please try again','gb_error');
                    break;
                default:
                    jQuery('#gb_replace_done').html('<p>'+response+'</p>');
                    jQuery('#gb_replace_done').css('display','block');
                    jQuery('#gb_replace_btn').css('display','none');
                    jQuery('#gb_replace_btn').attr('disabled','disabled');
                    jQuery('#gb_replace_message').css('display','none');
                    break;
            }
        });
    }
}

//Display/hide the attributes field
function show_attr(){
    var gb_html_tag = jQuery('.gb_with_field_element option:selected');
    if(gb_html_tag.val() != 'null'){
        jQuery('#gb_add_to_tag').show('slow');
        if(gb_html_tag.attr('data-attr')){
            jQuery('#gb_add_to_tag').find('.href').show();
        }
    }else{
        jQuery('#gb_add_to_tag').find('.noattr').hide();
        jQuery('#gb_add_to_tag').hide('slow');
    }
}

//Check the attributes fields for quotes and apostrophe
function gb_check_attr(item){
    if(item.val()!='' && item.attr('class') != 'free_text'){
        var reg = /^[^'";]*$/;
        var item_val = item.val();
        if(!reg.test(item_val)){
            item.addClass('input_not_clear');
        }else{
            item.removeClass('input_not_clear');
        }
    }
}

//Clear all the inputs in the tag's attr
function clearAllInputs(parent){
    parent.find('input').each(function(){
        jQuery(this).val('');
    });
}

//Close the attr fields
function close_attr(){
    jQuery('#gb_add_to_tag').addClass('gb_hide');
}

//Close the attr fields and Clear all the inputs in the tag's attr
function close_and_clear_attr(){
    if(jQuery('#gb_add_to_tag').hasClass('gb_hide')){
        jQuery('#gb_add_to_tag').removeClass('gb_hide');
    }else{
        close_attr();
        clearAllInputs(jQuery('#gb_add_to_tag').find('ul'));
    }
}

//gb_check_all
function gb_check_all(){
    if(jQuery('.gb_check_all').attr('data-check') == 'on'){
        jQuery('.gb_check_all').attr('data-check','off');
        jQuery('.gb_check_all').parent().find('ul').find('input[type="checkbox"]').prop('checked', false);
    }else{
        jQuery('.gb_check_all').attr('data-check','on');
        jQuery('.gb_check_all').parent().find('ul').find('input[type="checkbox"]').prop('checked', true);
    }
}

//Ajax subscribe
function gb_subscribe(){
    event.preventDefault();
    var all_good = true;
    jQuery('.gb_newsletter_message > p').hide();
    jQuery('.gb_newsletter_message').hide();
    jQuery('#form-wysija-2').find('.spinner').show();
    jQuery('#form-wysija-2').find('input[type="submit"]').attr("disabled", "disabled");
    jQuery('#form-wysija-2').find('input[class*="required"]').each(function(){
        if(jQuery(this).val() == ''){
            all_good = false;
        }
    });
    if(all_good){
        data = {
            action: 'gb_subscribe',
            data: jQuery('#form-wysija-2').serialize(),
            email: jQuery('#subscribe_mail').val()
        };
        jQuery.post(ajaxurl, data, function (response){
            switch (response.trim()){
                case '1':
                    jQuery('.gb_newsletter_message > p#gb_newsletter_error-1').css('display','inline-block');
                    jQuery('.gb_newsletter_message').show();
                    break;
                case '2':
                    jQuery('.gb_newsletter_message > p#gb_newsletter_error-2').css('display','inline-block');
                    jQuery('.gb_newsletter_message').show();
                    break;
                default:
                    jQuery('.gb_newsletter_message > p#gb_newsletter_OK').css('display','inline-block').html(response.trim());
                    jQuery('.gb_newsletter_message').show();
            }
            jQuery('#form-wysija-2').find('submit').removeAttr('disable');
            jQuery('#form-wysija-2').find('.spinner').hide();
        });
    }else{
        jQuery('.gb_newsletter_message > p#gb_newsletter_error-3').css('display','inline-block');
        jQuery('.gb_newsletter_message').show();
    }
}

//Ajax to show/hide the donation box
function gb_donation(donat){
    var checked = false;
    if(donat.is(':checked'))
        checked = true;
    data = {
        action: 'gb_donation',
        data: checked
    };
    donat.attr("disabled", true);
    jQuery.post(ajaxurl, data, function (response){
        jQuery('.gb_donate blockquote').html('<p class="gb_donate_blockquote_new">'+response+'</p>');
        if(!checked){
            jQuery('.gb_donate blockquote #gb_donate_blockquote').css('display','block');
            jQuery('.gb_donated_block').show();
        }else{
            jQuery('.gb_donate blockquote #gb_donate_blockquote').css('display','none');

            jQuery('.gb_donate blockquote').attr('data-from','GB-Web');
            jQuery('.gb_donated_block').hide();

        }
        donat.attr("disabled", false);
    });

}

//Set all gb_fallow_us elements
function set_gb_fallow_us(){

    jQuery('.gb_fallow_us').each(function(){
        var fallow_parent = jQuery(this).parent();
        while(fallow_parent.css('background-color') == 'rgba(0, 0, 0, 0)' && !fallow_parent.is("body")){
            fallow_parent = fallow_parent.parent();
        }
        jQuery(this).find('.round-div').css({
            border : Math.round(jQuery(this).width() *.7)+'px solid '+fallow_parent.css('background-color')

        });
    });

}






