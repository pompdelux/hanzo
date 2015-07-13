<?php
namespace Hanzo\Bundle\AdminBundle\Controller;

use Hanzo\Bundle\AdminBundle\Exporter\SeoTextExporter;
use Hanzo\Core\CoreController;
use Hanzo\Model\CmsQuery;
use Hanzo\Model\LanguagesQuery;
use Hanzo\Model\ProductsQuery;
use Hanzo\Model\ProductsSeoI18n;
use Hanzo\Model\ProductsSeoI18nQuery;
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
        $exporter = new SeoTextExporter();
        $exporter->setDBConnection($this->getDbConnection());

        $locale      = $request->request->get('locale');
        $exportTypes = $request->request->get('export_types');

        if (empty($exportTypes)) {
            $this->get('session')->getFlashBag()->add('warning', 'No export types selected');

            return $this->redirect($this->generateUrl('admin_tools_seo_index'));
        }


        if (!$locale) {
            $locale = LanguagesQuery::create()
                ->orderById()
                ->findOne($this->getDbConnection())
                ->getLocale();
        }

        $data = $exporter->getDataAsXML($locale, $exportTypes);

        return new Response($data, 200, [
            'Content-Type' => 'application/xml',
            'Content-Disposition' => 'attachment; filename="seo_texts_'.$locale.'.xml"',
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
        $fileTmp   = $request->files->get('language_file');
        $tmpDir    = sys_get_temp_dir();
        $fileName  = 'seo_text_import.xml';
        $inputFile = $tmpDir.'/'.$fileName;
        $errors    = [];
        $locale    = false;
        $products  = false;
        $cmsPages  = false;

        $dbLocale = LanguagesQuery::create()
            ->orderById()
            ->findOne($this->getDbConnection())
            ->getLocale();

        $fileTmp->move($tmpDir, $fileName);

        if (is_file($inputFile)) {
            $xml = simplexml_load_file($inputFile);
            foreach ($xml->children() as $child) {
                switch ($child->getName())
                {
                    case 'locale':
                        $locale = (string) $child;
                        break;

                    case 'products':
                        $products = $child;
                        break;

                    case 'cmspages':
                        $cmsPages = $child;
                        break;

                    default:
                        $errors[] = 'Unknown tag "'.$child->getName().'"';
                        break;
                }
            }

            if ($locale === false) {
                $errors[] = 'Missing <locale> tag';
                $this->get('session')->getFlashBag()->add('warning', 'Some errors happened: '.implode("<br>", $errors));

                return $this->redirect($this->generateUrl('admin_tools_seo_index'));
            }

            if ($dbLocale !== $locale) {
                $errors[] = 'Uploaded fields <locale> tag does not match the selected database: "'.$locale.'" vs. "'.$dbLocale.'"';
                $this->get('session')->getFlashBag()->add('warning', 'Some errors happened: '.implode("<br>", $errors));

                return $this->redirect($this->generateUrl('admin_tools_seo_index'));
            }

            $productsErrors = [];
            if ($products !== false) {
                $productsErrors = $this->importProductsTexts($products, $locale);
            }

            $cmsPagesErrors = [];
            if ($cmsPages !== false) {
                $cmsPagesErrors = $this->importCMSPagesTexts($cmsPages, $locale);
            }
        } else {
            $errors[] = 'No file uploaded';
        }

        $errors = array_merge($errors, $productsErrors, $cmsPagesErrors);
        if (!empty($errors)) {
            $this->get('session')->getFlashBag()->add('warning', 'Some errors happened: '.implode("<br>", $errors));
        } else {
            $this->get('session')->getFlashBag()->add('notice', 'Success, import complete.');
        }

        return $this->redirect($this->generateUrl('admin_tools_seo_index'));
    }

    /**
     * importProductsTexts
     *
     * @param SimpleXMLElement $products
     *
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     */
    protected function importProductsTexts(\SimpleXMLElement $products, $locale)
    {
        $errors = [];

        foreach ($products->children() as $product) {
            $seoText = ProductsSeoI18nQuery::create()
                ->filterByLocale($locale)
                ->filterByProductsId($product->id)
                ->findOne();

            if (!$seoText) {
                $seoText = new ProductsSeoI18n();
            }

            $productId   = (int) $product->id;
            $title       = (string) $product->metatitle;
            $description = (string) $product->metadescription;

            if (empty($title) && empty($description)) {
                continue;
            }

            if (strlen($title) > 255) {
                $errors[] = 'Title is more than 255 chars (Truncated in database):<br>'.$title.'<br>';
            }

            if (strlen($description) > 255) {
                $errors[] = 'Description is more than 255 chars (Truncated in database):<br>'.$description.'<br>';
            }

            $seoText->setProductsId($productId);
            $seoText->setMetaTitle($title);
            $seoText->setMetaDescription($description);
            $seoText->setLocale($locale);
            $seoText->save();
        }

        return $errors;
    }

    /**
     * importCMSPagesTexts
     *
     * @param SimpleXMLElement $pages
     *
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     */
    protected function importCMSPagesTexts(\SimpleXMLElement $pages, $locale)
    {
        $errors = [];

        foreach ($pages->children() as $page) {
            $seoText = CmsQuery::create()
                ->useCmsI18nQuery()
                    ->filterByLocale($locale)
                ->endUse()
                ->filterById($page->id)
                ->findOne();

            if (!$seoText) {
                // We will not create new CMS i18n entries
                $errors[] = 'Could not find CMS page for id "'.$page->id.'" ("'.$page->title.'")';
                continue;
            }

            $title       = (string) $page->metatitle;
            $description = (string) $page->metadescription;

            if (empty($title) && empty($description)) {
                continue;
            }

            if (strlen($title) > 255) {
                $errors[] = 'Title is more than 255 chars (Truncated in database):<br>'.$title.'<br>';
            }

            if (strlen($description) > 255) {
                $errors[] = 'Description is more than 255 chars (Truncated in database):<br>'.$description.'<br>';
            }

            $seoText->setMetaTitle($title);
            $seoText->setMetaDescription($description);
            $seoText->save();
        }

        return $errors;
    }
}
