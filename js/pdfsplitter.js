(function(OCA) {
    'use strict';
    
    console.log('PDFSplitter: Loading extension...');
    OCA.Files = OCA.Files || {};

    const PDFSplitter = {
        attach: function(fileList) {
            console.log('PDFSplitter: Attaching to fileList...');
            fileList.fileActions.registerAction({
                name: 'splitPDF',
                displayName: t('pdfsplitter', 'Split PDF'),
                mime: 'application/pdf',
                order: -140,
                permissions: OC.PERMISSION_UPDATE,
                iconClass: 'icon-pdfsplitter',
                shouldRender: function(file) {
                    console.log('PDFSplitter: Checking file:', file.name, 'mime:', file.mime);
                    return file.mime === 'application/pdf' || file.name.toLowerCase().endsWith('.pdf');
                },
                actionHandler: function(fileName, context) {
                    const dir = context.dir || '/';
                    const path = dir + '/' + fileName;

                    // Show loading spinner
                    OC.dialogs.info(
                        t('pdfsplitter', 'Splitting PDF...'),
                        t('pdfsplitter', 'Please wait')
                    );

                    // Call the API endpoint
                    $.ajax({
                        url: OC.generateUrl('/apps/pdfsplitter/split'),
                        type: 'POST',
                        data: {
                            source: path
                        },
                        success: function(response) {
                            if (response.status === 'success') {
                                // Refresh the file list
                                context.fileList.reload();
                                OC.dialogs.info(
                                    t('pdfsplitter', 'PDF has been split successfully.'),
                                    t('pdfsplitter', 'Success')
                                );
                            } else {
                                OC.dialogs.alert(
                                    response.message || t('pdfsplitter', 'An error occurred while splitting the PDF.'),
                                    t('pdfsplitter', 'Error')
                                );
                            }
                        },
                        error: function() {
                            OC.dialogs.alert(
                                t('pdfsplitter', 'An error occurred while splitting the PDF.'),
                                t('pdfsplitter', 'Error')
                            );
                        }
                    });
                }
            });
        }
    };

    OCA.Files.PDFSplitter = PDFSplitter;

    OC.Plugins.register('OCA.Files.FileList', OCA.Files.PDFSplitter);
})(OCA);
