<?php
namespace Barlian\ImagemapWizard\Tests\Unit\Domain\Model;

/**
 * Test case.
 *
 * @author Tolleiv Nietsch 
 * @author David Bruchmann <david.bruchmann@gmail.com>
 */
class ImagemapWizardTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{
    /**
     * @var \Barlian\ImagemapWizard\Domain\Model\ImagemapWizard
     */
    protected $subject = null;

    protected function setUp()
    {
        parent::setUp();
        $this->subject = new \Barlian\ImagemapWizard\Domain\Model\ImagemapWizard();
    }

    protected function tearDown()
    {
        parent::tearDown();
    }

    /**
     * @test
     */
    public function getTxImagemapwizardLinksReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getTxImagemapwizardLinks()
        );

    }

    /**
     * @test
     */
    public function setTxImagemapwizardLinksForStringSetsTxImagemapwizardLinks()
    {
        $this->subject->setTxImagemapwizardLinks('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'txImagemapwizardLinks',
            $this->subject
        );

    }
}
