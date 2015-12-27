<?php
/**
 * Created by PhpStorm.
 * User: eduardojunior
 * Date: 26/12/15
 * Time: 23:25
 */
namespace Escola\Entity;

use Core\Test\EntityTestCase;

/**
 * @group Entity
 */
class HabilitacaoTest extends EntityTestCase
{
    public function setup()
    {
        parent::setup();
    }

    /**
     * Check if filters exists
     */
    public function testGetInputFilter()
    {
        $tipoEnsino = new TipoEnsino();
        $if = $tipoEnsino->getInputFilter();
        $this->assertInstanceOf('Zend\InputFilter\InputFilter', $if);

        return $if;
    }

    /**
     * @depends testGetInputFilter
     */
    public function testInputFilterValid($if)
    {
        $this->assertEquals(4, $if->count());

        $this->assertTrue($if->has('id'));
        $this->assertTrue($if->has('nome'));
        $this->assertTrue($if->has('instituicao'));
        $this->assertTrue($if->has('ativo'));
    }

    /**
     * Teste insert data
     */
    public function testInsert()
    {
        $habilitacao = $this->buildHabilitacao();
        $habilitacao->setInstituicao($habilitacao);
        $this->em->persist($habilitacao);
        $this->em->flush();

        $this->assertNotNull($habilitacao->getId());
        $this->assertEquals(1, $habilitacao->getId());

        /**
         * get row from database
         */
        $savedHabilitacao = $this->em->find(get_class($habilitacao), $habilitacao->getId());

        $this->assertEquals(1, $savedHabilitacao->getId());
    }

    /**
     * @expectedException Core\Entity\EntityException
     */
    public function testInputFilterInvalidNome()
    {
        $habilitacao = $this->buildHabilitacao();
        $habilitacao->setNome('Mussum ipsum cacilds, vidis litro abertis. Consetis adipiscings elitis. Pra lá,
		depois divoltis porris, paradis. Paisis, filhis, espiritis santis. Mé faiz elementum
		girarzis, nisi eros vermeio, in elementis mé pra quem é amistosis quis leo. Manduma pindureta quium dia nois
		paga. Sapien in monti palavris qui num significa nadis i
		pareci latim. Interessantiss quisso pudia ce receita de bolis, mais bolis eu num gostis.');
        $this->em->persist($habilitacao);
        $this->em->flush();
    }

    public function testUpdate()
    {
        $habilitacao = $this->buildHabilitacao();
        $this->em->persist($habilitacao);
        $this->em->flush();

        $savedHabilitacao = $this->em->find(get_class($habilitacao), $habilitacao->getId());
        $this->assertEquals('Integral', $savedHabilitacao->getNome());
        $savedHabilitacao->setNome('Integral X');

        $this->em->flush();

        $savedHabilitacao = $this->em->find(get_class($habilitacao), $savedHabilitacao->getId());
        $this->assertEquals('Integral X', $savedHabilitacao->getNome());

    }

    public function testDelete()
    {
        $habilitacao = $this->buildHabilitacao();
        $this->em->persist($habilitacao);
        $this->em->flush();

        $id = $habilitacao->getId();
        $savedHabilitacao = $this->em->find(get_class($habilitacao), $id);
        $this->em->remove($savedHabilitacao);
        $this->em->flush();

        $savedHabilitacao = $this->em->find(get_class($habilitacao), $id);
        $this->assertNull($savedHabilitacao);

    }

    private function buildHabilitacao()
    {
        $habilitacao = new Habilitacao();
		$habilitacao->setNome('Integral');
		$instituicao = $this->buildInstituicao();
		$habilitacao->setInstituicao($instituicao);

        return $habilitacao;
    }

    private function buildInstituicao()
    {
        $instituicao = new Instituicao();
        $instituicao->setNome('Prefeitura Municipal Modelo');
        $instituicao->setResponsavel('Secretaria Municipal Modelo');

        return $instituicao;
    }

}
