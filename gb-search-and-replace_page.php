<?php

wp_enqueue_script(GB_REPLACE_SLUG.'-script',array( 'jquery' ));
wp_enqueue_style(GB_REPLACE_SLUG.'-style');
if(is_rtl()){
    wp_enqueue_style(GB_REPLACE_SLUG.'-style-rtl');
}
wp_enqueue_script('jquery-ui-dialog');
wp_enqueue_style("wp-jquery-ui-dialog");
$post_types = get_post_types();

$newsletter = get_option('gb_newsletter',false);
$donation = get_option('gb_donation');

?>

<div class="wrap gb_style">
    <header>
        <h1><?php echo GB_REPLACE_NAME; ?></h1>
        <h4><?php echo __('Choose where to search for a word, sentence or HTML tag and replace it.',GB_REPLACE_SLUG); ?></h4>
    </header>
    <section>
        <section class="gb_in_field_con">
            <label><?php echo __('Search In:',GB_REPLACE_SLUG); ?></label>
            <div class="gb_in_field gb_replace_field_look gb_box_shadow">
                <div class="gb_for_media gb_open_btn"></div>
                <a href="javascript:void(0);" class="gb_check_all" data-check="off"><span class="check"><?php echo __('Check All',GB_REPLACE_SLUG); ?></span><span class="uncheck"><?php echo __('Uncheck All',GB_REPLACE_SLUG); ?></span></a>
                <ul>
                    <?php
                    foreach($post_types as $post_type){
                        echo '<li>';
                        echo '<input type="checkbox" name="gb_replace_in[]" value="'.$post_type.'"> '.$post_type;
                        echo '</li>';
                    }
                    ?>
                </ul>
                <a href="javascript:void(0);" class="gb_check_all" data-check="off"><span class="check"><?php echo __('Check All',GB_REPLACE_SLUG); ?></span><span class="uncheck"><?php echo __('Uncheck All',GB_REPLACE_SLUG); ?></span></a>
            </div>
        </section>
        <section class="gb_replace_fields_con">
            <section class="gb_replace_field_con">
                <label><?php echo __('Search:',GB_REPLACE_SLUG); ?></label>
                <input type="text" class="gb_replace_field gb_replace_field_look gb_box_shadow" id="gb_replace_field">
                <div class="gb_replace_option"><input type="checkbox" id="gb_replace_case"> - <?php echo __('Case Sensitive',GB_REPLACE_SLUG); ?></div>
            </section>
            <section class="gb_with_field_con">
                <label><?php echo __('Replace:',GB_REPLACE_SLUG); ?></label>
                <input type="text" class="gb_with_field gb_replace_field_look gb_box_shadow" id="gb_with_field">
                <section class="gb_replace_tag_con">
                    <label><?php echo __('HTML Tag:',GB_REPLACE_SLUG); ?></label>
                    <select class="gb_with_field_element gb_box_shadow">
                        <option value="null"><?php echo __('Non',GB_REPLACE_SLUG); ?></option>
                        <option value="a" data-attr="href"><?php echo __('Link',GB_REPLACE_SLUG); ?> - a</option>
                        <option value="label"><?php echo __('Label',GB_REPLACE_SLUG); ?> - label</option>
                        <option value="h1"><?php echo __('Header',GB_REPLACE_SLUG); ?> 1 - H1</option>
                        <option value="h2"><?php echo __('Header',GB_REPLACE_SLUG); ?> 2 - H2</option>
                        <option value="h3"><?php echo __('Header',GB_REPLACE_SLUG); ?> 3 - H3</option>
                        <option value="h4"><?php echo __('Header',GB_REPLACE_SLUG); ?> 4 - H4</option>
                        <option value="h5"><?php echo __('Header',GB_REPLACE_SLUG); ?> 5 - H5</option>
                        <option value="h6"><?php echo __('Header',GB_REPLACE_SLUG); ?> 6 - H6</option>
                        <option value="p"><?php echo __('Paragraph',GB_REPLACE_SLUG); ?> - p</option>
                        <option value="strong"><?php echo __('Bold',GB_REPLACE_SLUG); ?> - strong</option>
                        <option value="i"><?php echo __('Italic',GB_REPLACE_SLUG); ?> - i</option>
                        <option value="del"><?php echo __('Delete',GB_REPLACE_SLUG); ?> - del</option>
                    </select>
                    <aside id="gb_add_to_tag">
                        <label><?php echo __('Attributes:',GB_REPLACE_SLUG); ?></label>
                        <a href="javascript:void(0);" class="gb_close_open"></a>
                        <ul class="gb_replace_field_look gb_box_shadow">
                            <li><span class="attrName">Id:</span><input type="text" name="addId"></li>
                            <li><span class="attrName">Class:</span><input type="text" name="addClass"></li>
                            <li><span class="attrName">Title:</span><input class="free_text" type="text" name="addTitle"></li>
                            <li class="noattr href"><span class="attrName">Href:</span><input type="text" name="addHref"></li>
                            <li><span class="attrName">Data: </span><br>
                                <ul>
                                    <li><span class="attrVal">Type-</span><input type="text" name="addData-Type"></li>
                                    <li><span class="attrVal">Value-</span><input class="free_text" type="text" id="TypeVal" name="TypeVal"></li>
                                </ul>
                            </li>
                            <li>
                                <button id="gb_save_attr" class="gb_button-green"><?php echo __('Save',GB_REPLACE_SLUG); ?></button>
                                <button id="gb_clear_attr" class="gb_button-red fright"><?php echo __('Clear',GB_REPLACE_SLUG); ?></button>
                            </li>
                        </ul>
                    </aside>
                </section>
            </section>
            <section class="gb_btn_field_con">
                <button class="button-primary" id="gb_replace_btn-view"><?php echo __('Go',GB_REPLACE_SLUG); ?></button>
                <button class="gb_button-green" id="gb_replace_btn"><?php echo __('Replace',GB_REPLACE_SLUG); ?></button>
            </section>
            <section id="gb_replace_message" class="gb_replace_field_look"></section>
            <section id="gb_replace_done" class="gb_replace_field_done"></section>
        </section>
        <aside id="gb_ads" class="fright">
            <header><h3>Related stuff:</h3></header>
            <section>
                <div id="gb_donated_con" class="gb_box_shadow">
                    <div class="gb_box_color-gb">
                        <div class="gb_donate">
                            <p>
                                <input type="checkbox" id="gb_donated" <?php echo $donation != '0' ? 'checked':''?>> <i><?php echo __('- I appreciate your work and made my contribution',GB_REPLACE_SLUG); ?></i>
                            <blockquote class="gb_box_shadow" data-from="Napoleon Hill">
                                <p id="gb_donate_blockquote">
                                    <?php
                                    if($donation == '0'){
                                        echo __("If you appreciate the kindness shown you by others, say it with deeds as well as words.",GB_REPLACE_SLUG);
                                    }else{
                                        echo __('Thank you for helping us',GB_REPLACE_SLUG);
                                    }
                                    ?>

                                </p>
                            </blockquote>
                            </p>
                            <div class="gb_donated_block <?php echo $donation != '0' ? 'gb_donated':''?>">
                                <p><?php echo __('If you use and like this plugin, please help us to improve, upgrade, and create new free plugins by donating:',GB_REPLACE_SLUG); ?>
                                </p>
                                <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top"><input type="hidden" name="cmd" value="_s-xclick" />
                                    <input type="hidden" name="hosted_button_id" value="2AJ2QDUWU39P2" />
                                    <input type="image" alt="PayPal - The safer, easier way to pay online!" name="submit" src="https://www.paypalobjects.com/en_US/IL/i/btn/btn_donateCC_LG.gif" />
                                    <img alt="" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1" border="0" /></form>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="gb_fallow_us" class="gb_box_shadow">
                    <div class="gb_box_color-orange gb_fallow_padding">
                        <div class="gb_fallow_con">
                            <div class="gb_fallow_us">
                                <article class="">
                                    <section class="gbfu-thumbnail">
                                        <div class="gbfu-wrapper"><div class="round-div"></div>
                                            <a class="round-div no-effect" target="_blanck" href="http://gb-plugins.com/"></a>
                                            <img src="<?php echo GB_REPLACE_SRC.'images/gb_fallow_us/gb_plugins.jpg'; ?>" title="GB-Plugins.com" alt="GB-Plugins.com">
                                        </div>
                                    </section>
                                </article>
                            </div>
                            <a href="http://gb-plugins.com/" target="_blank">GB-Plugins</a>
                        </div>
                        <div class="gb_fallow_con">
                            <div class="gb_fallow_us">
                                <article class="">
                                    <section class="gbfu-thumbnail">
                                        <div class="gbfu-wrapper"><div class="round-div"></div>
                                            <a class="round-div no-effect" target="_blanck" href="https://plus.google.com/+GbpluginsSlideshow/posts"></a>
                                            <img src="<?php echo GB_REPLACE_SRC.'images/gb_fallow_us/googleplus.jpg'; ?>" title="plus.google.com" alt="plus.google.com">
                                        </div>
                                    </section>
                                </article>
                            </div>
                            <a href="https://plus.google.com/+GbpluginsSlideshow/posts" target="_blank">Google+</a>
                        </div>
                        <div class="gb_fallow_con">
                            <div class="gb_fallow_us">
                                <article class="">
                                    <section class="gbfu-thumbnail">
                                        <div class="gbfu-wrapper"><div class="round-div"></div>
                                            <a class="round-div no-effect" target="_blanck" href="http://www.youtube.com/user/GBGallerySlideshow"></a>
                                            <img src="<?php echo GB_REPLACE_SRC.'images/gb_fallow_us/youtube.jpg'; ?>" title="www.youtube.com" alt="www.youtube.com">
                                        </div>
                                    </section>
                                </article>
                            </div>
                            <a href="http://www.youtube.com/user/GBGallerySlideshow" target="_blank">YouTube</a>
                        </div>
                        <div class="gb_fallow_con">
                            <div class="gb_fallow_us">
                                <article class="">
                                    <section class="gbfu-thumbnail">
                                        <div class="gbfu-wrapper"><div class="round-div"></div>
                                            <a class="round-div no-effect" target="_blanck" href="https://www.facebook.com/GBPlugins"></a>
                                            <img src="<?php echo GB_REPLACE_SRC.'images/gb_fallow_us/facebook.jpg'; ?>" title="www.facebook.com" alt="www.facebook.com">
                                        </div>
                                    </section>
                                </article>
                            </div>
                            <a href="https://www.facebook.com/GBPlugins" target="_blank">facebook</a>
                        </div>
                        <div class="gb_fallow_con">
                            <div class="gb_fallow_us">
                                <article class="">
                                    <section class="gbfu-thumbnail">
                                        <div class="gbfu-wrapper"><div class="round-div"></div>
                                            <a class="round-div no-effect" target="_blanck" href="https://twitter.com/GBGallerySlider"></a>
                                            <img src="<?php echo GB_REPLACE_SRC.'images/gb_fallow_us/twitter.jpg'; ?>" title="twitter.com" alt="twitter.com">
                                        </div>
                                    </section>
                                </article>
                            </div>
                            <a href="https://twitter.com/GBGallerySlider" target="_blank">Twitter</a>
                        </div>
                        <div class="gb_fallow_con">
                            <div class="gb_fallow_us">
                                <article class="">
                                    <section class="gbfu-thumbnail">
                                        <div class="gbfu-wrapper"><div class="round-div"></div>
                                            <a class="round-div no-effect" target="_blanck" href="http://www.linkedin.com/groups/GB-Plugins-6592297"></a>
                                            <img src="<?php echo GB_REPLACE_SRC.'images/gb_fallow_us/linkedin.jpg'; ?>" title="www.linkedin.com" alt="www.linkedin.com">
                                        </div>
                                    </section>
                                </article>
                            </div>
                            <a href="http://www.linkedin.com/groups/GB-Plugins-6592297" target="_blank">Linkedin</a>
                        </div>
                    </div>
                </div>
                <?php if(!$newsletter): ?>
                    <div id="newsletter" class="gb_box_shadow">
                        <div class="gb_box_color-blue">
                            <form id="form-wysija-2" class="widget_wysija">
                                <p class="wysija-paragraph">
                                    <label><?php echo __('First name',GB_REPLACE_SLUG); ?> <span class="wysija-required">*</span></label>
                                    <input type="text" name="wysija[user][firstname]" class="wysija-input validate[required]" title="First name" value="">
                                        <span class="abs-req">
                                            <input type="text" name="wysija[user][abs][firstname]" class="wysija-input validated[abs][firstname]" value="">
                                        </span>
                                </p>
                                <p class="wysija-paragraph">
                                    <label><?php echo __('Email',GB_REPLACE_SLUG); ?> <span class="wysija-required">*</span></label>
                                    <input type="text" id="subscribe_mail" name="wysija[user][email]" class="wysija-input validate[required,custom[email]]" title="Email" value="">
                                        <span class="abs-req">
                                            <input type="text" name="wysija[user][abs][email]" class="wysija-input validated[abs][email]" value="">
                                        </span>
                                </p>
                                <div class="gb_newsletter_btn_con">
                                    <span class="spinner"></span>
                                    <input class="wysija-submit wysija-submit-field gb_button-green" type="submit" value="SUBSCRIBE !">
                                </div>
                                <input type="hidden" name="form_id" value="3">
                                <input type="hidden" name="action" value="save">
                                <input type="hidden" name="controller" value="subscribers">
                                <input type="hidden" value="1" name="wysija-page">
                                <input type="hidden" name="wysija[user_list][list_ids]" value="4">
                            </form>

                            <div class="gb_newsletter_message">
                                <p id="gb_newsletter_error-1"><?php echo __('Error in data transfer, please try again',GB_REPLACE_SLUG); ?></p>
                                <p id="gb_newsletter_error-2"><?php echo __('Sorry, your email is not valid ',GB_REPLACE_SLUG); ?></p>
                                <p id="gb_newsletter_error-3"><?php echo __('Please insert data to all fields',GB_REPLACE_SLUG); ?></p>
                                <p id="gb_newsletter_OK"></p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </section>
            <footer><?php echo __('Thank you for using',GB_REPLACE_SLUG); ?> <a target="_blanck" href="http://gb-plugins.com/">GB-plugins</a></footer>
        </aside>
    </section>
    <footer>
        <article class="gb_replace_details_con gb_replace_field_look gb_box_shadow"></article>
    </footer>
    <section class="gb_helper">
        <section class="gb_the_dialog" data-title="<?php echo __('Caution: There are hidden search words in HTML tags',GB_REPLACE_SLUG); ?>" data-ok="<?php echo __('Yes i am sure',GB_REPLACE_SLUG); ?>" data-no="<?php echo __('Cancel',GB_REPLACE_SLUG); ?>">
            <div class="gb_dialog_content">
                <p><?php echo __('There are hidden search words in HTML tags on:',GB_REPLACE_SLUG); ?></p>
                <ul class="gb_dialog_list"></ul>
                <p><?php echo __('If you choose to continue it might affect the structure of the tag and/or your site',GB_REPLACE_SLUG); ?></p>
                <p><?php echo __('Are you sure you want to continue?',GB_REPLACE_SLUG); ?></p>
            </div>
        </section>
    </section>
</div>