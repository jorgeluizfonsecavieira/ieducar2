<?php
/**
 * Created by PhpStorm.
 * User: eduardojunior
 * Date: 13/05/16
 * Time: 09:08
 */
use Escola\Entity\ComodoFuncao;

/**
 * @group Controller
 */
class ComodoFuncaoControllerTest extends \Core\Test\ControllerTestCase
{
    /**
     * Namespace completa do controller
     * @var string PredioController
     */
    protected $controllerFQDN = 'Escola\Controller\ComodoFuncaoController';

    /**
     * Nome da rota, geralmente o nome do modulo
     * @var string escola
     */
    protected $controllerRoute = 'escola';

    /***
     * testa a pagina inicial, listando os dados
     * @return void
     */
    public function testComodoFuncaoIndexAction()
    {
        $rowA = $this->buildComodoFuncao();
        $rowB = $this->buildComodoFuncao();
        $rowB->setNome('Outro Nome');
        $this->em->persist($rowA);
        $this->em->persist($rowB);
        $this->em->flush();

        // invoca a rota index
        $this->routeMatch->setParam('action', 'index');
        $result = $this->controller->dispatch(
            $this->request, $this->response
        );

        // verifica o response
        $response = $this->controller->getResponse();
        $this->assertEquals(200, $response->getStatusCode());

        // testa se um ViewModel foi retornado
        $this->assertInstanceOf('Zend\View\Model\ViewModel', $result);

        // testa os dados da View
        $variables = $result->getVariables();

        $this->assertArrayHasKey('dados', $variables);

        // faz a comparacao dos dados
        $paginator = $variables['dados'];
        $this->assertEquals($rowA->getNome(), $paginator->getItem(1)->getNome());
        $this->assertEquals($rowB->getNome(), $paginator->getItem(2)->getNome());
    }

    /**
     * testa a tela de inclusao de um novo registro
     * @return void
     */
    public function testComodoFuncaoSaveActionNewRequest()
    {
        // dispara a acao
        $this->routeMatch->setParam('action', 'save');
        $result = $this->controller->dispatch(
            $this->request, $this->response
        );

        // verifica a resposta
        $response = $this->controller->getResponse();
        $this->assertEquals(200, $response->getStatusCode());

        // testa se recebeu um ViewModel
        $this->assertInstanceOf('Zend\View\Model\ViewModel', $result);

        // verifica se existe um form
        $variables = $result->getVariables();
        $this->assertInstanceOf('Zend\Form\Form', $variables['form']);
        $form = $variables['form'];
        // testa os itens do formulario
        $id = $form->get('id');
        $this->assertEquals('id', $id->getName());
        $this->assertEquals('hidden', $id->getAttribute('type'));

        $nome = $form->get('nome');
        $this->assertEquals('nome', $nome->getName());
        $this->assertEquals('text', $nome->getAttribute('type'));


        $descricao = $form->get('descricao');
        $this->assertEquals('descricao', $descricao->getName());
        $this->assertEquals('textarea', $descricao->getAttribute('type'));

        $ativo = $form->get('ativo');
        $this->assertEquals('ativo', $ativo->getName());
        $this->assertEquals('Zend\Form\Element\Select', $ativo->getAttribute('type'));
    }

    /**
     * testa a tela de alteracoes de um registro
     * @return void
     */
    public function testComodoFuncaoSaveActionUpdateFormRequest()
    {
        $entity = $this->buildComodoFuncao();
        $this->em->persist($entity);
        $this->em->flush();

        $this->routeMatch->setParam('action', 'save');
        $this->routeMatch->setParam('id', $entity->getId());
        $result = $this->controller->dispatch(
            $this->request, $this->response
        );

        // verifica a resposta
        $this->assertInstanceOf('Zend\View\Model\ViewModel', $result);
        $variables = $result->getVariables();

        // verifica se existe um form
        $this->assertInstanceOf('Zend\Form\Form', $variables['form']);
        $form = $variables['form'];

        // testa os itens do formulario
        $id = $form->get('id');
        $nome = $form->get('nome');
        $descricao = $form->get('descricao');
        $ativo = $form->get('ativo');
        $this->assertEquals('id', $id->getName());
        $this->assertEquals($entity->getId(), $id->getValue());
        $this->assertEquals($entity->getNome(), $nome->getValue());
        $this->assertEquals($entity->getDescricao(), $descricao->getValue());
        $this->assertEquals($entity->isAtivo(), $ativo->getValue());
    }

    /**
     * testa a inclusao de um novo registro
     * @return void
     */
    public function testComodoFuncaoSaveActionPostRequest()
    {
        // dispara a acao
        $this->routeMatch->setParam('action', 'save');
        $this->request->setMethod('post');
        $this->request->getPost()->set('id', '');
        $this->request->getPost()->set('nome', 'Nome');
        $this->request->getPost()->set('descricao', 'Descrição');
        $this->request->getPost()->set('ativo', true);
        $result = $this->controller->dispatch(
            $this->request, $this->response
        );

        // verifica a resposta
        $response = $this->controller->getResponse();
        // a pagina redireciona, entao o status = 302
        $this->assertEquals(302, $response->getStatusCode());
        $headers = $response->getHeaders();
        $this->assertEquals('Location: /escola/comodo-funcao', $headers->get('Location'));
    }


    /**
     * testa o update de um registro
     */
    public function testComodoFuncaoUpdateAction()
    {
        $entity = $this->buildComodoFuncao();
        $this->em->persist($entity);
        $this->em->flush();

        // dispara a acao
        $this->routeMatch->setParam('action', 'save');
        $this->request->setMethod('post');
        $this->request->getPost()->set('id', $entity->getId());
        $this->request->getPost()->set('nome', 'Outro Nome');
        $this->request->getPost()->set('descricao', $entity->getDescricao());
        $this->request->getPost()->set('ativo', $entity->isAtivo());

        $result = $this->controller->dispatch(
            $this->request, $this->response
        );

        // verifica a resposta
        $response = $this->controller->getResponse();
        // a pagina redireciona, entao o status = 302
        $this->assertEquals(302, $response->getStatusCode());
        $headers = $response->getHeaders();
        $this->assertEquals('Location: /escola/comodo-funcao', $headers->get('Location'));

        $savedEntity = $this->em->find(get_class($entity), $entity->getId());
        $this->assertEquals('Outro Nome', $savedEntity->getNome());
    }

    /**
     * testa a inclusao, formulario invalido e nome vazio
     */
    public function testComodoFuncaoSaveActionInvalidFormPostRequest()
    {
        // dispara a acao
        $this->routeMatch->setParam('action', 'save');
        $this->request->setMethod('post');
        $this->request->getPost()->set('id', '');
        $this->request->getPost()->set('nome', '');
        $this->request->getPost()->set('descricao', '');
        $this->request->getPost()->set('ativo', true);

        $result = $this->controller->dispatch(
            $this->request, $this->response
        );

        // verifica a resposta
        $response = $this->controller->getResponse();

        // a pagina nao redireciona por causa do erro, entao o status = 200
        $this->assertEquals(200, $response->getStatusCode());
        $headers = $response->getHeaders();

        // verify filters validators
        $msgs = $result->getVariables()['form']->getMessages();
        $this->assertEquals('Value is required and can\'t be empty', $msgs["nome"]['isEmpty']);
    }


    /**
     * testa a busca com resultados
     */
    public function testComodoFuncaoPostActionRequest()
    {
        $rowA = $this->buildComodoFuncao();
        $rowB = $this->buildComodoFuncao();
        $rowB->setNome('Outro Nome');
        $this->em->persist($rowA);
        $this->em->persist($rowB);
        $this->em->flush();

        // invoca a rota index
        $this->routeMatch->setParam('action', 'busca');
        $this->request->getPost()->set('q', 'Outro Nome');

        $result = $this->controller->dispatch(
            $this->request, $this->response
        );

        // verifica o response
        $response = $this->controller->getResponse();
        $this->assertEquals(200, $response->getStatusCode());

        // testa os dados da View
        $variables = $result->getVariables();

        // faz a comparacao dos dados
        $dados = $variables['dados'];
        $this->assertEquals($rowB->getNome(), $dados[0]->getNome());
    }

    /**
     * testa a exclusao sem passar o id
     * @expectedException Exception
     * @expectedExceptionMesage Código Obrigatório
     */
    public function testComodoFuncaoInvalidDeleteAction()
    {
        // dispara aa acao
        $this->routeMatch->setParam('action', 'delete');
        $result = $this->controller->dispatch(
            $this->request, $this->response
        );

        // verifica a resposta
        $response = $this->controller->getResponse();
    }

    /**
     * testa a exclusao
     */
    public function testComodoFuncaoDeleteAction()
    {
        $entity = $this->buildComodoFuncao();
        $this->em->persist($entity);
        $this->em->flush();

        // dispara a acao
        $this->routeMatch->setParam('action', 'delete');
        $this->routeMatch->setParam('id', $entity->getId());

        $result = $this->controller->dispatch(
            $this->request, $this->response
        );

        // verifica a resposta
        $response = $this->controller->getResponse();

        // a pagina redireciona, entao o status = 302
        $this->assertEquals(302, $response->getStatusCode());
        $headers = $response->getHeaders();
        $this->assertEquals('Location: /escola/comodo-funcao', $headers->get('Location'));
    }


    /**
     * Testa a tela de detalhes
     */
    public function testComodoFuncaoDetalhesAction()
    {
        $entity = $this->buildComodoFuncao();

        $this->em->persist($entity);
        $this->em->flush();

        // Dispara a acao
        $this->routeMatch->setParam('action', 'detalhes');
        $this->routeMatch->setParam('id', $entity->getId());

        $result = $this->controller->dispatch(
            $this->request, $this->response
        );

        // Verifica a resposta
        $response = $this->controller->getResponse();
        $this->assertEquals(200, $response->getStatusCode());

        //	Testa se um ViewModel foi retornado
        $this->assertInstanceOf('Zend\View\Model\ViewModel', $result);


        //	Testa os dados da View
        $variables = $result->getVariables();
        $this->assertArrayHasKey('data', $variables);

        //	Faz a comparação dos dados
        $data = $variables["data"];
        $this->assertEquals($entity->getNome(), $data->getNome());

    }

    /**
     * Testa visualizaçao de detalhes de um id inexistente
     * @expectedException Exception
     * @expectedExceptionMessage Registro não encontrado
     */
    public function testComodoFuncaoDetalhesInvalidIdAction()
    {
        $this->routeMatch->setParam('action', 'detalhes');
        $this->routeMatch->setParam('id', -1);

        $result = $this->controller->dispatch(
            $this->request, $this->response
        );
        //	Verifica a resposta
        $response = $this->controller->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * Testa a exlusao passando um id inexistente
     * @expectedException Exception
     * @expectedExceptionMessage Registro não encontrado
     */
    public function testComodoFuncaoInvalidIdDeleteAction()
    {
        $entity = $this->buildComodoFuncao();
        $this->em->persist($entity);
        $this->em->flush();

        //	Dispara a acao
        $this->routeMatch->setParam('action', 'delete');
        $this->routeMatch->setParam('id', 2);

        $result = $this->controller->dispatch(
            $this->request, $this->response
        );

        //	Verifica a resposta
        $response = $this->controller->getResponse();

        //	A pagina redireciona, entao o status = 302
        $this->assertEquals(302, $response->getStatusCode());
        $headers = $response->getHeaders();
        $this->assertEquals(
            'Location: /escola/comodo-funcao', $headers->get('Location')
        );
    }

    /**
     * @return ComodoFuncao
     */
    private function buildComodoFuncao()
    {
        $entity = new ComodoFuncao();
        $entity->setNome('Nome');
        $entity->setDescricao('Descrição');
        $entity->setAtivo(true);

        return $entity;
    }
}