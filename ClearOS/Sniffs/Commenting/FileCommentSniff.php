<?php
/**
 * Parses and verifies the doc comments for files.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   CVS: $Id: FileCommentSniff.php 301632 2010-07-28 01:57:56Z squiz $
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

if (class_exists('PHP_CodeSniffer_CommentParser_ClassCommentParser', true) === false) {
    throw new PHP_CodeSniffer_Exception('Class PHP_CodeSniffer_CommentParser_ClassCommentParser not found');
}

/**
 * Parses and verifies the doc comments for files.
 *
 * Verifies that :
 * <ul>
 *  <li>A doc comment exists.</li>
 *  <li>There is a blank newline after the short description.</li>
 *  <li>There is a blank newline between the long and short description.</li>
 *  <li>There is a blank newline between the long description and tags.</li>
 *  <li>A PHP version is specified.</li>
 *  <li>Check the order of the tags.</li>
 *  <li>Check the indentation of each tag.</li>
 *  <li>Check required and optional tags and the format of their content.</li>
 * </ul>
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   Release: 1.3.0RC1
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

class ClearOS_Sniffs_Commenting_FileCommentSniff extends PEAR_Sniffs_Commenting_FileCommentSniff
{
    // ClearFoundation - do not check for "PHP Version" tag
    protected function processPHPVersion($commentStart, $commentEnd, $commentText)
    {
        return;
    }

    // Allow all lower case below
    protected function processCategory($errorPos)
    {
        $category = $this->commentParser->getCategory();
        if ($category !== null) {
            $content = $category->getContent();
            $content = ucfirst($content); // ClearFoundation, allow lower case
            if ($content !== '') {
                if (PHP_CodeSniffer::isUnderscoreName($content) !== true) {
                    $newContent = str_replace(' ', '_', $content);
                    $nameBits   = explode('_', $newContent);
                    $firstBit   = array_shift($nameBits);
                    $newName    = ucfirst($firstBit).'_';
                    foreach ($nameBits as $bit) {
                        $newName .= ucfirst($bit).'_';
                    }

                    $error     = 'Category name "%s" is not valid; consider "%s" instead';
                    $validName = trim($newName, '_');
                    $data      = array(
                                  $content,
                                  $validName,
                                 );
                    $this->currentFile->addError($error, $errorPos, 'InvalidCategory', $data);
                }
            } else {
                $error = '@category tag must contain a name';
                $this->currentFile->addError($error, $errorPos, 'EmptyCategory');
            }
        }
    }

    protected function processPackage($errorPos)
    {
        $package = $this->commentParser->getPackage();
        if ($package === null) {
            return;
        }

        $content = $package->getContent();
        if ($content === '') {
            $error = '@package tag must contain a name';
            $this->currentFile->addError($error, $errorPos, 'EmptyPackage');
            return;
        }

        $content = ucfirst($content); // ClearFoundation, allow lower case
        if (PHP_CodeSniffer::isUnderscoreName($content) === true) {
            return;
        }

        $newContent = str_replace(' ', '_', $content);
        $newContent = preg_replace('/[^A-Za-z_]/', '', $newContent);
        $nameBits   = explode('_', $newContent);
        $firstBit   = array_shift($nameBits);
        $newName    = strtoupper($firstBit{0}).substr($firstBit, 1).'_';
        foreach ($nameBits as $bit) {
            $newName .= strtoupper($bit{0}).substr($bit, 1).'_';
        }

        $error     = 'Package name "%s" is not valid; consider "%s" instead';
        $validName = trim($newName, '_');
        $data      = array(
                      $content,
                      $validName,
                     );
        $this->currentFile->addError($error, $errorPos, 'InvalidPackage', $data);

    }

    protected function processSubpackage($errorPos)
    {
        $package = $this->commentParser->getSubpackage();
        if ($package !== null) {
            $content = $package->getContent();
            $content = ucfirst($content); // ClearFoundation, allow lower case
            if ($content !== '') {
                if (PHP_CodeSniffer::isUnderscoreName($content) !== true) {
                    $newContent = str_replace(' ', '_', $content);
                    $nameBits   = explode('_', $newContent);
                    $firstBit   = array_shift($nameBits);
                    $newName    = strtoupper($firstBit{0}).substr($firstBit, 1).'_';
                    foreach ($nameBits as $bit) {
                        $newName .= strtoupper($bit{0}).substr($bit, 1).'_';
                    }

                    $error     = 'Subpackage name "%s" is not valid; consider "%s" instead';
                    $validName = trim($newName, '_');
                    $data      = array(
                                  $content,
                                  $validName,
                                 );
                    $this->currentFile->addError($error, $errorPos, 'InvalidSubpackage', $data);
                }
            } else {
                $error = '@subpackage tag must contain a name';
                $this->currentFile->addError($error, $errorPos, 'EmptySubpackage');
            }
        }
    }
}

?>
