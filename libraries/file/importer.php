<?php

/*
 * NuevebitFileImporter
 * 
 * Does the same as c5's FileImporter, but allows to specify the FileStorageLocation
 * for the imported file.
 * 
 */

Loader::library("file/importer");

class NuevebitFileImporter {
    
	public function import($pointer, $filename = false, $fr = false, $fsl = 0, $verifyExtensionAllowed = true) {
		if ($filename == false) {
			// determine filename from $pointer
			$filename = basename($pointer);
		}
		
		$fh = Loader::helper('validation/file');
		$fi = Loader::helper('file');
		$filename = $fi->sanitize($filename);
		
		// test if file is valid, else return FileImporter::E_FILE_INVALID
		if (!$fh->file($pointer)) {
			return FileImporter::E_FILE_INVALID;
		}
		
		if ($verifyExtensionAllowed && !$fh->extension($filename)) {
			return FileImporter::E_FILE_INVALID_EXTENSION;
		}

		$prefix = $this->generatePrefix();
		
		// do save in the FileVersions table
		
		// move file to correct area in the filesystem based on prefix
		$response = $this->storeFile($prefix, $pointer, $filename, $fr, $fsl);
		if (!$response) {
			return FileImporter::E_FILE_UNABLE_TO_STORE;
		}
		
		if (!($fr instanceof File)) {
			// we have to create a new file object for this file version
			$fv = $this->importFile($filename, $prefix, $fsl);
			$fv->refreshAttributes(true);
			$fr = $fv->getFile();
		} else {
			// We get a new version to modify
			$fv = $fr->getVersionToModify(true);
			$fv->updateFile($filename, $prefix);
			$fv->refreshAttributes();
		}

		$fr->refreshCache();
		return $fv;
    }

    private function importFile($filename, $prefix, $fsl, $data = array()) {
		$db = Loader::db();
		$dh = Loader::helper('date');
		$date = $dh->getSystemDateTime(); 
		
		$uID = 0;
		$u = new User();
		if (isset($data['uID'])) {
			$uID = $data['uID'];
		} else if ($u->isRegistered()) {
			$uID = $u->getUserID();
		}

        if ($fsl != 0) {
            $db->Execute('insert into Files (fDateAdded, uID, fslID) values (?, ?, ?)', array($date, $uID, $fsl->getID()));
        } else {
            $db->Execute('insert into Files (fDateAdded, uID) values (?, ?)', array($date, $uID));
        }
		
		$fID = $db->Insert_ID();

		$f = File::getByID($fID);
		
		$fv = $f->addVersion($filename, $prefix, $data);
		Events::fire('on_file_add', $f, $fv);
			
		return $fv;
    }

	private function storeFile($prefix, $pointer, $filename, $fr = false, $fsl = 0) {
		// assumes prefix are 12 digits
		$fi = Loader::helper('concrete/file');
		$path = false;
		if ($fr instanceof File) {
			if ($fr->getStorageLocationID() > 0) {
				Loader::model('file_storage_location');
				$fsl = FileStorageLocation::getByID($fr->getStorageLocationID());
				$path = $fi->mapSystemPath($prefix, $filename, true, $fsl->getDirectory());
			}
		}
		
		if ($path == false) {
            $dir = $fsl != 0 ? $fsl->getDirectory() : DIR_FILES_UPLOADED;
			$path = $fi->mapSystemPath($prefix, $filename, true, $dir);
		}
		$r = @copy($pointer, $path);
		@chmod($path, FILE_PERMISSIONS_MODE);
		return $r;
	}

	private function generatePrefix() {
		$prefix = rand(10, 99) . time();
		return $prefix;	
	}
	
}
?>
