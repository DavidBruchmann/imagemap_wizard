<?php
namespace Barlian\ImagemapWizard\Tests\Unit\Controller;

/**
 * Test case.
 *
 * @author Tolleiv Nietsch 
 * @author David Bruchmann <david.bruchmann@gmail.com>
 */
class ImagemapWizardControllerTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{
    /**
     * @var \Barlian\ImagemapWizard\Controller\ImagemapWizardController
     */
    protected $subject = null;

    protected function setUp()
    {
        parent::setUp();
        $this->subject = $this->getMockBuilder(\Barlian\ImagemapWizard\Controller\ImagemapWizardController::class)
            ->setMethods(['redirect', 'forward', 'addFlashMessage'])
            ->disableOriginalConstructor()
            ->getMock();
    }

    protected function tearDown()
    {
        parent::tearDown();
    }

    /**
     * @test
     */
    public function listActionFetchesAllImagemapWizardsFromRepositoryAndAssignsThemToView()
    {

        $allImagemapWizards = $this->getMockBuilder(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class)
            ->disableOriginalConstructor()
            ->getMock();

        $imagemapWizardRepository = $this->getMockBuilder(\Barlian\ImagemapWizard\Domain\Repository\ImagemapWizardRepository::class)
            ->setMethods(['findAll'])
            ->disableOriginalConstructor()
            ->getMock();
        $imagemapWizardRepository->expects(self::once())->method('findAll')->will(self::returnValue($allImagemapWizards));
        $this->inject($this->subject, 'imagemapWizardRepository', $imagemapWizardRepository);

        $view = $this->getMockBuilder(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class)->getMock();
        $view->expects(self::once())->method('assign')->with('imagemapWizards', $allImagemapWizards);
        $this->inject($this->subject, 'view', $view);

        $this->subject->listAction();
    }

    /**
     * @test
     */
    public function showActionAssignsTheGivenImagemapWizardToView()
    {
        $imagemapWizard = new \Barlian\ImagemapWizard\Domain\Model\ImagemapWizard();

        $view = $this->getMockBuilder(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class)->getMock();
        $this->inject($this->subject, 'view', $view);
        $view->expects(self::once())->method('assign')->with('imagemapWizard', $imagemapWizard);

        $this->subject->showAction($imagemapWizard);
    }

    /**
     * @test
     */
    public function createActionAddsTheGivenImagemapWizardToImagemapWizardRepository()
    {
        $imagemapWizard = new \Barlian\ImagemapWizard\Domain\Model\ImagemapWizard();

        $imagemapWizardRepository = $this->getMockBuilder(\Barlian\ImagemapWizard\Domain\Repository\ImagemapWizardRepository::class)
            ->setMethods(['add'])
            ->disableOriginalConstructor()
            ->getMock();

        $imagemapWizardRepository->expects(self::once())->method('add')->with($imagemapWizard);
        $this->inject($this->subject, 'imagemapWizardRepository', $imagemapWizardRepository);

        $this->subject->createAction($imagemapWizard);
    }

    /**
     * @test
     */
    public function editActionAssignsTheGivenImagemapWizardToView()
    {
        $imagemapWizard = new \Barlian\ImagemapWizard\Domain\Model\ImagemapWizard();

        $view = $this->getMockBuilder(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class)->getMock();
        $this->inject($this->subject, 'view', $view);
        $view->expects(self::once())->method('assign')->with('imagemapWizard', $imagemapWizard);

        $this->subject->editAction($imagemapWizard);
    }

    /**
     * @test
     */
    public function updateActionUpdatesTheGivenImagemapWizardInImagemapWizardRepository()
    {
        $imagemapWizard = new \Barlian\ImagemapWizard\Domain\Model\ImagemapWizard();

        $imagemapWizardRepository = $this->getMockBuilder(\Barlian\ImagemapWizard\Domain\Repository\ImagemapWizardRepository::class)
            ->setMethods(['update'])
            ->disableOriginalConstructor()
            ->getMock();

        $imagemapWizardRepository->expects(self::once())->method('update')->with($imagemapWizard);
        $this->inject($this->subject, 'imagemapWizardRepository', $imagemapWizardRepository);

        $this->subject->updateAction($imagemapWizard);
    }

    /**
     * @test
     */
    public function deleteActionRemovesTheGivenImagemapWizardFromImagemapWizardRepository()
    {
        $imagemapWizard = new \Barlian\ImagemapWizard\Domain\Model\ImagemapWizard();

        $imagemapWizardRepository = $this->getMockBuilder(\Barlian\ImagemapWizard\Domain\Repository\ImagemapWizardRepository::class)
            ->setMethods(['remove'])
            ->disableOriginalConstructor()
            ->getMock();

        $imagemapWizardRepository->expects(self::once())->method('remove')->with($imagemapWizard);
        $this->inject($this->subject, 'imagemapWizardRepository', $imagemapWizardRepository);

        $this->subject->deleteAction($imagemapWizard);
    }

    /**
     * @test
     */
    public function showActionAssignsTheGivenImagemapWizardToView()
    {
        $imagemapWizard = new \Barlian\ImagemapWizard\Domain\Model\ImagemapWizard();

        $view = $this->getMockBuilder(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class)->getMock();
        $this->inject($this->subject, 'view', $view);
        $view->expects(self::once())->method('assign')->with('imagemapWizard', $imagemapWizard);

        $this->subject->showAction($imagemapWizard);
    }
}
