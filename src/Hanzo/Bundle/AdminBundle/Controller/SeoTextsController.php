<?php

namespace Hanzo\Bundle\AdminBundle\Controller;

use Hanzo\Bundle\AdminBundle\Exporter\SeoCSVTextExporter;
use Hanzo\Bundle\AdminBundle\Importer\SeoCsvTextImporter;
use Hanzo\Bundle\AdminBundle\Importer\SeoTextImporterException;
use Hanzo\Bundle\AdminBundle\Importer\SeoXMLTextImporter;
use Hanzo\Core\CoreController;
use Hanzo\Model\LanguagesQuery;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ToolsController
 *
 * @package Hanzo\Bundle\AdminBundle
 */
class SeoTextsController extends CoreController
{
    /**
     * indexAction
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @author Henrik Farre <hf@bellcom.dk>
     */
    public function indexAction(Request $request)
    {
        return $this->render('AdminBundle:Tools:seoTexts.html.twig', [
            'database' => $request->getSession()->get('database'),
        ]);
    }

    /**
     * exportAction
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @author Henrik Farre <hf@bellcom.dk>
     */
    public function exportAction(Request $request)
    {
        #$exporter = new SeoXMLTextExporter();
        $exporter = new SeoCSVTextExporter(';');
        $exporter->setDBConnection($this->getDbConnection());

        $locale     = $request->request->get('locale');
        $exportType = $request->request->get('export_type');

        if (!in_array($exportType, ['products', 'cms'])) {
            $this->get('session')->getFlashBag()->add('warning', 'No export types selected');

            return $this->redirect($this->generateUrl('admin_tools_seo_index'));
        }

        if (!$locale) {
            $locale = LanguagesQuery::create()
                ->orderById()
                ->findOne($this->getDbConnection())
                ->getLocale();
        }

        $data = $exporter->getData($locale, $exportType);

        return new Response("\xEF\xBB\xBF".$data, 200, [
            'Content-Encoding'    => 'UTF-8',
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="seo-texts-'.$exportType.'-'.$locale.'.csv"',
        ]);
    }

    /**
     * importAction
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @author Henrik Farre <hf@bellcom.dk>
     */
    public function importAction(Request $request)
    {
        /** @var \Symfony\Component\HttpFoundation\File\UploadedFile $uploadedFile */
        $uploadedFile = $request->files->get('language_file');
        $fileName = $uploadedFile->getClientOriginalName();

        $dbLocale = LanguagesQuery::create()
            ->orderById()
            ->findOne($this->getDbConnection())
            ->getLocale();

        $file = $uploadedFile->move(sys_get_temp_dir(), $fileName);
        $mimeType = strtolower($uploadedFile->getClientMimeType());

        // We determine importer based on the input files mime type.
        // Currently csv and xml files are accepted.
        if (in_array($mimeType, ['application/vnd.ms-excel', 'text/csv'])) {
            $importer = new SeoCsvTextImporter($dbLocale, $this->getDbConnection());
        } elseif ('text/xml' == $mimeType) {
            $importer = new SeoXMLTextImporter($dbLocale, $this->getDbConnection());
        } else {
            $this->get('session')->getFlashBag()->add('warning', 'Unsupported file format, only use xml or csv files.');

            return $this->redirect($this->generateUrl('admin_tools_seo_index'));
        }

        try {
            $importer->handle($file);
            $this->get('session')->getFlashBag()->add('notice', 'Success, import complete.');
        } catch (SeoTextImporterException $e) {
            $this->get('session')->getFlashBag()->add('warning', $e->getMessage());
        }

        return $this->redirect($this->generateUrl('admin_tools_seo_index'));
    }
}
