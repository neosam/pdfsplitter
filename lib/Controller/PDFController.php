<?php
namespace OCA\PDFSplitter\Controller;

use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\JSONResponse;
use OCP\Files\IRootFolder;
use OCP\IRequest;
use OCP\IUserSession;

class PDFController extends Controller {
    private $rootFolder;
    private $userSession;

    public function __construct(
        $appName,
        IRequest $request,
        IRootFolder $rootFolder,
        IUserSession $userSession
    ) {
        parent::__construct($appName, $request);
        $this->rootFolder = $rootFolder;
        $this->userSession = $userSession;
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function split($source) {
        $user = $this->userSession->getUser();
        $userFolder = $this->rootFolder->getUserFolder($user->getUID());
        
        try {
            $file = $userFolder->get($source);
            $content = $file->getContent();
            
            // Create a temporary file
            $tempFile = tempnam(sys_get_temp_dir(), 'pdf_');
            file_put_contents($tempFile, $content);
            
            // Use python script to split PDF
            $outputDir = dirname($tempFile);
            $cmd = sprintf('/usr/bin/env python3 %s/lib/python/split_pdf.py "%s" "%s"', 
                          __DIR__ . '/../..', 
                          $tempFile, 
                          $outputDir);
            
            exec($cmd, $output, $returnVar);
            
            if ($returnVar !== 0) {
                throw new \Exception('Failed to split PDF');
            }
            
            // Get the directory path of the original file
            $targetDir = dirname($source);
            
            // Upload split pages
            $splitFiles = glob($outputDir . '/split_*.pdf');
            foreach ($splitFiles as $splitFile) {
                $basename = basename($splitFile);
                $targetPath = $targetDir . '/' . $basename;
                
                $content = file_get_contents($splitFile);
                $userFolder->newFile($targetPath, $content);
                
                unlink($splitFile);
            }
            
            // Cleanup
            unlink($tempFile);
            
            return new JSONResponse(['status' => 'success']);
        } catch (\Exception $e) {
            return new JSONResponse(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
