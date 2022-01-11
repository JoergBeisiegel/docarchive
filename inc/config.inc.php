<?php
/***********************************************************************************************************************/


		/*****************************************************/
		/********** Konfiguration der PHP-Anwendung **********/
		/*****************************************************/

		// Konstante definieren
		// Konstanten werden in PHP mittels der Funktion define() definiert.
		// Konstanten besitzen im Gegensatz zu Variablen kein $-Präfix
		// Üblicherweise werden Konstanten komplett GROSS geschrieben.


/***********************************************************************************************************************/


		/********** DB-KONFIGURATION **********/
		define("DB_SYSTEM", "mysql");
		define("DB_HOST", "localhost");
		define("DB_PORT", "3306");
		define("DB_NAME", "document_archive");
		define("DB_USER", "root");
		define("DB_PWD", "");


/***********************************************************************************************************************/


		/********** FORMULAR-KONFIGURATION **********/
		define("MIN_INPUT_LENGTH", 2);
		define("MAX_INPUT_LENGTH", 255);


/***********************************************************************************************************************/


		/********** BILDUPLOAD-KONFIGURATION **********/
		define("IMAGE_UPLOAD_PATH", "uploaded_images/");
		define("IMAGE_ALLOWED_MIMETYPES", array("image/jpeg", "image/jpg", "image/png", "image/gif"));
		define("IMAGE_MAX_SIZE", 128000);
		define("IMAGE_MAX_WIDTH", 800);
		define("IMAGE_MAX_HEIGHT", 600);
		define("AVATAR_DUMMY_PATH", "css/images/avatar_dummy.png");


/***********************************************************************************************************************/


		/********** DEBUGGING EIN-/AUSSCHALTEN **********/
		define("DEBUG", true);
		define("DEBUG_DB", false);
		define("DEBUG_F", false);


/***********************************************************************************************************************/


		/********** Blogtitel **********/
		// define("BLOG_TITLE", "Vizcas Blog");
		define("BLOG_TITLE", "PHP-Projekt Blog");


/***********************************************************************************************************************/
?>