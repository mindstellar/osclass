<?php if (!defined('OC_ADMIN')) {
    exit('Direct access is not allowed.');
}
/*
 * Osclass - software for creating and publishing online classified advertising platforms
 * Maintained and supported by Mindstellar Community
 * https://github.com/mindstellar/Osclass
 * Copyright (c) 2021.  Mindstellar
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 *                     GNU GENERAL PUBLIC LICENSE
 *                        Version 3, 29 June 2007
 *
 *  Copyright (C) 2007 Free Software Foundation, Inc. <http://fsf.org/>
 *  Everyone is permitted to copy and distribute verbatim copies
 *  of this license document, but changing it is not allowed.
 *
 *  You should have received a copy of the GNU Affero General Public
 *  License along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 */

function addHelp()
{
    echo '<p>'
         . __("Install or uninstall the plugins available in your installation. In some cases, "
              . "you'll have to configure the plugin in order to get it to work.")
         . '</p>';
}


osc_add_hook('help_box', 'addHelp');

function customPageHeader()
{
?>
<h1><?php _e('Manage Plugins'); ?>
    <a href="#" class="ms-1 bi bi-question-circle-fill float-right" data-bs-target="#help-box" data-bs-toggle="collapse"
       href="#help-box"></a>
    <a href="<?php echo osc_admin_base_url(true); ?>?page=plugins&amp;action=add"
       class="ms-1 text-success float-end" data-bs-toggle="tooltip" data-bs-placement="bottom" title="<?php _e('Add plugin'); ?>"><i
                class="bi bi-plus-circle-fill"></i></a>
</h1>

<?php osc_show_flash_message('admin'); ?>
<?php if (Params::getParam('error') != '') { ?>
<!-- flash message -->
<div class="flashmessage flashmessage-error" style="display:block">
    <?php _e("Plugin couldn't be installed because it triggered a <strong>fatal error</strong>"); ?>
    <a class="btn ico btn-mini ico-close">x</a>
    <iframe style="border:0;" width="100%" height="60"
            src="<?php echo osc_admin_base_url(true); ?>?page=plugins&amp;action=error_plugin&amp;plugin=<?php
            echo Params::getParam('error'); ?>"></iframe>
    <!-- /flash message -->
    <?php } ?>
    <?php
    }
    osc_add_hook('admin_page_header', 'customPageHeader');

    /**
     * @param $string
     *
     * @return string
     */
    function customPageTitle($string)
    {
        return sprintf(__('Plugins &raquo; %s'), $string);
    }


    osc_add_filter('admin_title', 'customPageTitle');

    //customize Head
    function customHead()
    {
        ?>
        <script type="text/javascript">
            $(document).ready(function () {
                $('input:hidden[name="installed"]').each(function () {
                    $(this).parent().parent().children().css('background', 'none');
                    if ($(this).val() == '1') {
                        if ($(this).attr("enabled") == 1) {
                            $(this).parent().parent().css('background-color', '#EDFFDF');
                        } else {
                            $(this).parent().parent().css('background-color', '#FFFFDF');
                        }
                    } else {
                        $(this).parent().parent().css('background-color', '#FFF0DF');
                    }
                });

                // dialog delete
                $("#dialog-uninstall").dialog({
                    autoOpen: false,
                    modal: true,
                    title: '<?php echo osc_esc_js(__('Uninstall plugin')); ?>'
                });

                $('.plugin-tooltip').each(function () {
                    $(this).osc_tooltip('<?php echo osc_esc_js(__('Problems with this plugin? Ask for support.')); ?>', {
                        layout: 'gray-tooltip',
                        position: {x: 'right', y: 'middle'}
                    });
                });


            });

            // dialog delete function
            function uninstall_dialog(plugin, title) {
                $("#dialog-uninstall input[name='plugin']").attr('value', plugin);
                $("#dialog-uninstall").dialog('option', 'title', title);
                $("#dialog-uninstall").dialog('open');
                return false;
            }
        </script>
        <?php
    }


    osc_add_hook('admin_header', 'customHead', 10);

    $iDisplayLength = __get('iDisplayLength');
    $aData          = __get('aPlugins');

    $tab_index = 2;
    ?>
    <?php osc_current_admin_theme_path('parts/header.php'); ?>
    <div id="tabs" class="ui-osc-tabs ui-tabs-right">
        <ul style="display: none;">
            <?php
            $aPluginsToUpdate = json_decode(osc_get_preference('plugins_to_update'));
            $bPluginsToUpdate = is_array($aPluginsToUpdate) ? true : false;
            if ($bPluginsToUpdate && count($aPluginsToUpdate) > 0) {
                $tab_index = 0;
                ?>
                <li><a href="#update-plugins"><?php _e('Updates'); ?></a></li>
            <?php } ?>
            <li><a href="#upload-plugins"><?php _e('Available plugins'); ?></a></li>
        </ul>
        <div id="upload-plugins">
            <table class="table" cellpadding="0" cellspacing="0">
                <thead>
                <tr>
                    <th><?php _e('Name'); ?></th>
                    <th colspan=""><?php _e('Description'); ?></th>
                    <th> &nbsp;</th>
                    <th> &nbsp;</th>
                    <th> &nbsp;</th>
                    <th> &nbsp;</th>
                    <th> &nbsp;</th>
                </tr>
                </thead>
                <tbody>
                <?php if (count($aData['aaData']) > 0) : ?>
                    <?php foreach ($aData['aaData'] as $array) : ?>
                        <tr>
                            <?php foreach ($array as $key => $value) : ?>
                                <td>
                                    <?php echo $value; ?>
                                </td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="6" class="text-center">
                            <p><?php _e('No data available in table'); ?></p>
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
            <?php
            function showingResults()
            {
                $aData = __get('aPlugins');
                echo '<ul class="showing-results"><li><span>' . osc_pagination_showing((Params::getParam('iPage') - 1)
                                                                                       * $aData['iDisplayLength'] + 1,
                                                                                       ((Params::getParam('iPage') - 1)
                                                                                        * $aData['iDisplayLength'])
                                                                                       + count($aData['aaData']),
                                                                                       $aData['iTotalDisplayRecords'])
                     . '</span></li></ul>';
            }


            osc_add_hook('before_show_pagination_admin', 'showingResults');
            osc_show_pagination_admin($aData);
            ?>

            <div class="display-select-bottom">
                <form method="get" action="<?php echo osc_admin_base_url(true); ?>" class="inline nocsrf">
                    <?php foreach (Params::getParamsAsArray('get') as $key => $value) { ?>
                        <?php if ($key !== 'iDisplayLength') { ?>
                            <input type="hidden" name="<?php echo osc_esc_html($key); ?>"
                                   value="<?php echo osc_esc_html($value); ?>"/>
                        <?php }
                    } ?>
                    <select name="iDisplayLength" class="select-box-extra select-box-medium float-left"
                            onchange="this.form.submit();">
                        <option value="10" <?php if (Params::getParam('iDisplayLength') == 10) {
                            echo 'selected';
                        } ?> ><?php printf(__('%d plugins'), 10); ?></option>
                        <option value="25" <?php if (Params::getParam('iDisplayLength') == 25) {
                            echo 'selected';
                        } ?> ><?php printf(__('%d plugins'), 25); ?></option>
                        <option value="50" <?php if (Params::getParam('iDisplayLength') == 50) {
                            echo 'selected';
                        } ?> ><?php printf(__('%d plugins'), 50); ?></option>
                        <option value="100" <?php if (Params::getParam('iDisplayLength') == 100) {
                            echo 'selected';
                        } ?> ><?php printf(__('%d plugins'), 100); ?></option>
                    </select>
                </form>
            </div>
        </div>
        <?php if ($bPluginsToUpdate && count($aPluginsToUpdate) > 0) { ?>
            <div id="update-plugins">
                <?php
                $aIndex = array();
                if ($bPluginsToUpdate) {
                    $array_aux = array_keys($aData['aaInfo']);

                    foreach ($aPluginsToUpdate as $slug) {
                        $key = array_search($slug, $array_aux, true);
                        if ($key !== false) {
                            $aIndex[] = $aData['aaData'][$key];
                        }
                    }
                }
                ?>
                <table class="table" cellpadding="0" cellspacing="0">
                    <thead>
                    <tr>
                        <th><?php _e('Name'); ?></th>
                        <th colspan=""><?php _e('Description'); ?></th>
                        <th> &nbsp;</th>
                        <th> &nbsp;</th>
                        <th> &nbsp;</th>
                        <th> &nbsp;</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (count($aIndex) > 0) : ?>
                        <?php foreach ($aIndex as $array) : ?>
                            <tr>
                                <?php foreach ($array as $key => $value) : ?>
                                    <td>
                                        <?php echo $value; ?>
                                    </td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="6" class="text-center">
                                <p><?php _e('No data available in table'); ?></p>
                            </td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        <?php } ?>
        <div id="market_installer" class="has-form-actions hide">
            <form action="" method="post">
                <input type="hidden" name="market_code" id="market_code" value=""/>
                <div class="osc-modal-content-market">
                    <img src="" id="market_thumb" class="float-left"/>
                    <table class="table" cellpadding="0" cellspacing="0">
                        <tbody>
                        <tr class="table-first-row">
                            <td><?php _e('Name'); ?></td>
                            <td><span id="market_name"><?php _e('Loading data'); ?></span></td>
                        </tr>
                        <tr class="even">
                            <td><?php _e('Version'); ?></td>
                            <td><span id="market_version"><?php _e('Loading data'); ?></span></td>
                        </tr>
                        <tr>
                            <td><?php _e('Author'); ?></td>
                            <td><span id="market_author"><?php _e('Loading data'); ?></span></td>
                        </tr>
                        <tr class="even">
                            <td><?php _e('URL'); ?></td>
                            <td><span id="market_url_span"><a id="market_url"
                                                              href="#"><?php _e('Download manually'); ?></a></span></td>
                        </tr>
                        </tbody>
                    </table>
                    <div class="clear"></div>
                </div>
                <div class="form-actions">
                    <div class="wrapper">
                        <button id="market_cancel" class="btn btn-red"><?php _e('Cancel'); ?></button>
                        <button id="market_install" class="btn btn-submit"><?php _e('Continue install'); ?></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <form id="dialog-uninstall" method="get" action="<?php echo osc_admin_base_url(true); ?>"
          class="has-form-actions hide">
        <input type="hidden" name="page" value="plugins"/>
        <input type="hidden" name="action" value="uninstall"/>
        <input type="hidden" name="plugin" value=""/>
        <div class="form-horizontal">
            <div class="form-row">
                <?php _e('This action can not be undone. Uninstalling plugins may result in a permanent loss of data. '
                         . 'Are you sure you want to continue?'); ?>
            </div>
            <div class="form-actions">
                <div class="wrapper">
                    <a class="btn btn-dim" href="javascript:void(0);"
                       onclick="$('#dialog-uninstall').dialog('close');"><?php _e('Cancel'); ?></a>
                    <input id="uninstall-submit" type="submit" value="<?php echo osc_esc_html(__('Uninstall')); ?>"
                           class="btn btn-red"/>
                </div>
            </div>
        </div>
    </form>
    <script>
        $(function () {
            var tab_id = decodeURI(self.document.location.hash.substring(1));
            if (tab_id != '') {
                $("#tabs").tabs({active: <?php echo $tab_index; ?> });
                $('html, body').animate({scrollTop: 0}, 'slow');
            } else {
                $("#tabs").tabs({active: -1});
            }

            $("#market_cancel").on("click", function () {
                $(".ui-dialog-content").dialog("close");
                return false;
            });
        });

        function delete_plugin(plugin) {
            const x = confirm('<?php echo osc_esc_js(__('You are about to delete the files of the plugin. Do you want to continue?'))?>');
            if (x) {
                window.location = '<?php echo osc_admin_base_url(true) . '?page=plugins&action=delete&'
                                              . osc_csrf_token_url() . '&plugin='; ?>' + plugin;
            }
        }
    </script>
    <?php osc_current_admin_theme_path('parts/footer.php'); ?>
