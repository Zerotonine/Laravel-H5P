/*
 *
 * @Project
 * @Copyright      Djoudi
 * @Created        2018-02-20
 * @Filename       laravel-h5p.js
 * @Description
 *
 */
document.addEventListener('livewire:load', () => {
    window.livewire.on('editorOpened', (nonce, contentPath, id) => {
        initEditor(nonce, contentPath, id);
    });
}, false);

// document.addEventListener('DOMContentLoaded', () => {
//     initEditor();
// },false);


const initEditor = (nonce, contentPath, id) => {
    (function ($) {
        ns.init = function () {
            ns.$ = H5P.jQuery;

            if (H5PIntegration !== undefined && H5PIntegration.editor !== undefined) {
                ns.basePath = H5PIntegration.editor.libraryUrl;
                ns.fileIcon = H5PIntegration.editor.fileIcon;
                if(nonce){
                    ns.ajaxPath = H5PIntegration.editor.ajaxPath + nonce + (id ? `/${id}/` : '/');
                } else {
                    ns.ajaxPath = H5PIntegration.editor.ajaxPath;
                }

                if(contentPath){
                    ns.filesPath = contentPath;
                } else {
                    ns.filesPath = H5PIntegration.editor.filesPath;
                }

                ns.apiVersion = H5PIntegration.editor.apiVersion;
                // Semantics describing what copyright information can be stored for media.
                ns.copyrightSemantics = H5PIntegration.editor.copyrightSemantics;
                // Required styles and scripts for the editor
                ns.assets = H5PIntegration.editor.assets;
                // Required for assets
                ns.baseUrl = '';
                ns.metadataSemantics = H5PIntegration.editor.metadataSemantics
                if (H5PIntegration.editor.nodeVersionId !== undefined) {
                    ns.contentId = H5PIntegration.editor.nodeVersionId;
                }

                var h5peditor;
                var $editor = $('#laravel-h5p-editor');
                var $create = $('#laravel-h5p-create').hide();
                var $params = $('#laravel-h5p-parameters');
                var $library = $('#laravel-h5p-library');
                var library = $library.val();

                if (h5peditor === undefined) {
                    h5peditor = new ns.Editor(library, $params.val(), $editor[0]);
                }
                $create.show();


                $('#laravel-h5p-form').submit(function () {
                    if (h5peditor !== undefined) {
                        var params = h5peditor.getParams();

                        if (params !== undefined) {
                            $library.val(h5peditor.getLibrary());
                            $params.val(JSON.stringify(params));
                        } else {
                            return false;
                        }
                    }

                    $(this).find('.btn').button('loading');
                });

                // Title label
                var $title = $('#laravel-h5p-title');
                var $label = $title.prev();
                $title.focus(function () {
                    $label.addClass('screen-reader-text');
                }).blur(function () {

                    if ($title.val() === '') {
                        ns.getAjaxUrl('libraries')
                        $label.removeClass('screen-reader-text');
                    }
                }).focus();

                // Delete confirm
                $('#laravel-h5p-destory').click(function () {
                    return confirm(H5PIntegration.editor.deleteMessage);
                });
            }


        }


        ns.getAjaxUrl = function (action, parameters) {
            let url = ns.ajaxPath + action;
            if (parameters !== undefined) {
            var separator = url.indexOf('?') === -1 ? '?' : '&';
            for (var property in parameters) {
                if (parameters.hasOwnProperty(property)) {
                url += separator + property + '=' + parameters[property];
                separator = '&';
                }
            }
            }

            return url;
        };



        $(document).ready(ns.init);


    })(H5P.jQuery);
}
