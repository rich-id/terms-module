<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Tests\Infrastructure\FormType;

use RichCongress\TestFramework\TestConfiguration\Attribute\TestConfig;
use RichCongress\TestSuite\TestCase\ControllerTestCase;
use RichCongress\WebTestBundle\TestCase\TestTrait\WithSessionTrait;
use RichId\TermsModuleBundle\Domain\Entity\Terms;
use RichId\TermsModuleBundle\Domain\Entity\TermsVersion;
use RichId\TermsModuleBundle\Domain\Model\TermsEdition;
use RichId\TermsModuleBundle\Infrastructure\FormType\TermsVersionFormType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/** @covers \RichId\TermsModuleBundle\Infrastructure\FormType\TermsVersionFormType */
#[TestConfig('kernel')]
final class TermsVersionFormTypeTest extends ControllerTestCase
{
    use WithSessionTrait;

    /** @var FormFactoryInterface */
    public $formFactory;

    public function testSubmitEmpty(): void
    {
        $terms = new Terms();
        $termsVersion = new TermsVersion();
        $termsVersion->setTerms($terms);

        $model = new TermsEdition($termsVersion);
        $form = $this->formFactory->create(TermsVersionFormType::class, $model);

        $this->withSession($this->getClient(), fn(SessionInterface $e) => $form->submit([
            '_token' => $this->getCsrfToken(TermsVersionFormType::class)
        ]));

        $this->assertTrue($form->isSynchronized());
        $this->assertFalse($form->isValid());

        $output = $form->getData();
        $this->assertNull($output->getTitle());
        $this->assertNull($output->getContent());
        $this->assertNull($output->getPublicationDate());
        $this->assertNull($output->isTermsPublished());
        $this->assertNull($output->needVersionActivation());
        $this->assertSame($termsVersion, $output->getEntity());
    }

    public function testSubmitValidDataMinimum(): void
    {
        $terms = new Terms();
        $termsVersion = new TermsVersion();
        $termsVersion->setTerms($terms);

        $model = new TermsEdition($termsVersion);
        $form = $this->formFactory->create(TermsVersionFormType::class, $model);

        $this->withSession($this->getClient(), fn(SessionInterface $e) => $form->submit([
            'title'            => 'My title',
            'content'          => 'My content',
            'isTermsPublished' => '0',
            '_token'           => $this->getCsrfToken(TermsVersionFormType::class),
        ]));

        $this->assertTrue($form->isSynchronized());
        $this->assertTrue($form->isValid());

        $output = $form->getData();
        $this->assertSame('My title', $output->getTitle());
        $this->assertSame('My content', $output->getContent());
        $this->assertFalse($output->isTermsPublished());
        $this->assertNull($output->getPublicationDate());
        $this->assertNull($output->needVersionActivation());
        $this->assertSame($termsVersion, $output->getEntity());
    }

    public function testSubmitValidDataFull(): void
    {
        $terms = new Terms();
        $termsVersion = new TermsVersion();
        $termsVersion->setTerms($terms);

        $model = new TermsEdition($termsVersion);
        $form = $this->formFactory->create(TermsVersionFormType::class, $model);

        $this->withSession($this->getClient(), fn(SessionInterface $e) => $form->submit([
            'title'                 => 'My title',
            'content'               => 'My content',
            'publicationDate'       => '2021-01-01',
            'isTermsPublished'      => '0',
            'needVersionActivation' => 'true',
            '_token'                => $this->getCsrfToken(TermsVersionFormType::class),
        ]));

        $this->assertTrue($form->isSynchronized());
        $this->assertTrue($form->isValid());

        $output = $form->getData();
        $this->assertSame('My title', $output->getTitle());
        $this->assertSame('My content', $output->getContent());
        $this->assertSame('2021-01-01', $output->getPublicationDate()->format('Y-m-d'));
        $this->assertFalse($output->isTermsPublished());
        $this->assertTrue($output->needVersionActivation());
        $this->assertSame($termsVersion, $output->getEntity());
    }
}
