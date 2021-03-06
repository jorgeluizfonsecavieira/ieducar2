<?php
namespace Usuario\Form;

use Zend\Form\Form;
use Doctrine\ORM\EntityManager;
use Doctrine\Common\Persistence\ObjectManager;
// use Usuario\Entity\EnderecoExterno;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;

class Fisica extends Form
{
	//public function __construct(EntityManager $em)
    public function __construct(ObjectManager $objectManager)
	{
        $em = $objectManager;
		parent::__construct('fisica');
		$this->setAttribute('method', 'post');
		$this->setAttribute('action', '/usuario/fisica/save');
        $this->setAttribute('enctype', 'multipart/form-data');
		// $this->setAttribute('class', 'form-inline');

		//$this->setHydrator(new DoctrineHydrator($em, 'Usuario\Entity\Fisica'))
//              ->setObject(new \Usuario\Entity\Fisica());
        $this->setHydrator(new DoctrineHydrator($objectManager))->setObject(new \Usuario\Entity\Fisica());

        //set all validators by yourself
		$this->setUseInputFilterDefaults(false);
        //$this->setUseAsBaseFieldset(true);


        // $this->setAttribute('method', 'post')
        // 	 ->setHydrator(new ClassMethodsHydrator(false))
        //      ->setInputFilter(new InputFilter());

		$this->add(array(
			'name' => 'id',
			'attributes' => array(
				'type' => 'hidden',
			),
		));

		$this->add(array(
			'name' => 'nome',
			'attributes' => array(
				'type' => 'text',
				'class' => 'form-control',
                'required' => 'required'
				// 'style' => 'width:510px'
			),
			'options' => array(
				'label' => 'Nome:'
			),
		));
		

		$this->add(array(
			'type' => 'Zend\Form\Element\Select',
			'name' => 'situacao',
			'attributes' => array(
				'type' => 'Zend\Form\Element\Select',				
			),			
			'options' => array(
				'label' => 'Situação:',
				'value_options' => array(
					'A'	=> 'Ativo',
					'P' => 'Provisorio',
					'I' => 'Inativo'
				),				
			),
			'attributes' => array(
				'value' => 'A',
				'class' => 'form-control'
			),
		));		

		$this->add(array(
			'name' => 'dataNasc',
			'type' => 'text',
			'attributes' => array(
//				'type' => 'text',
				'class' => 'form-control dataNasc'
			),
			'options' => array(
				'label' => 'Data de Nascimento:',
			),
		));

		$this->add(array(
			'name' => 'sexo',
			'type' => 'Zend\Form\Element\Select',
			'attributes' => array(
				'type' => 'Zend\Form\Element\Select',
				//'value' => 'M',
				'class' => 'form-control'
			),
			'options' => array(
                'empty_option' => 'Selecione',
				'label' => 'Sexo:',
				'value_options' => array(
					'M' => 'Masculino',
					'F' => 'Feminino'
				),
			),			
		));



        $this->add(array(
            'name' => 'estadoCivil',
            'attributes' => array(
                'type' => 'DoctrineModule\Form\Element\ObjectSelect',
                'class' => 'form-control chosen-select',
                'style' => 'height:100px;',
            ),
            'type' => 'DoctrineModule\Form\Element\ObjectSelect',
            'options' => array(
                'label' => 'Estado Cívil:',
                'object_manager' => $em,
                'target_class' => 'Usuario\Entity\EstadoCivil',
                'property' => 'descricao',
                'find_method' => array(
                    'name' => 'findBy',
                    'params' => array(
                        'criteria' => array(),
                        'orderBy' => array('descricao' => 'ASC')
                    ),
                ),
                'display_empty_item' => true,
                'empty_item_label' => 'Selecione',
            ),
        ));

		$this->add(array(
			'name' => 'raca',
			'attributes' => array(
				'type' => 'DoctrineModule\Form\Element\ObjectSelect',
				'class' => 'form-control chosen-select',
				'style' => 'height:100px;',                
			),
			'type' => 'DoctrineModule\Form\Element\ObjectSelect',
			'options' => array(
				'label' => 'Raça:',
				//'empty_option' => 'Selecione',
                'allow_empty' => true,
                'continue_if_empty' => false,
				'object_manager' => $em,
				'target_class' => 'Usuario\Entity\Raca',
				'property' => 'nome',
				'find_method' => array(
					'name' => 'findBy',
					'params' => array(
						'criteria' => array('ativo' => true),
						'orderBy' => array('nome' => 'ASC')
					),
				),
                'display_empty_item' => true,
                'empty_item_label'   => 'Selecione',
			),
		));

        // @todo mudar o metodo de criterio... filtrar resultados com situacao A e provisorio alem de excluir a
        // propria pessoa da busca caso seja um edit
		$this->add(array(
            'name' => 'pessoaMae',
            'attributes' => array(
                'type' => 'DoctrineModule\Form\Element\ObjectSelect',
                'class' => 'form-control chosen-select',
                'style' => 'height:100px;',
            ),
            'type' => 'DoctrineModule\Form\Element\ObjectSelect',
            'options' => array(
                'label' => 'Mãe:',
                'object_manager' => $em,
                'target_class' => 'Usuario\Entity\Fisica',
                'property' => 'nome',
                'find_method' => array(
                    'name' => 'findBy',
                    'params' => array(
                        'criteria' => array('sexo' => 'F', 'situacao' => 'A'),
                        'orderBy' => array('nome' => 'ASC')
                    ),
                ),
                'display_empty_item' => true,
                'empty_item_label' => 'Informe o nome da mãe, CPF, ou RG da pessoa',
                'label_generator' => function($em) {
                    $label = '';
                    if ($em->getNome()){
                        $label .=  $em->getNome();
                    }
                    if ($em->getCpf()){
                        $label .= ' - CPF (' . $em->getCpf() . ')';
                    }
                    /*
                    if ($em->getRg()){
                        $label .= ' - ' . $em->getRg();
                    }*/

                    return $label;

                },
            ),
		));

        $this->add(array(
            'name' => 'pessoaPai',
            'attributes' => array(
                'type' => 'DoctrineModule\Form\Element\ObjectSelect',
                'class' => 'form-control chosen-select',
                'style' => 'height:100px;',
            ),
            'type' => 'DoctrineModule\Form\Element\ObjectSelect',
            'options' => array(
                'label' => 'Pai:',
                'object_manager' => $em,
                'target_class' => 'Usuario\Entity\Fisica',
                'property' => 'nome',
                'find_method' => array(
                    'name' => 'findBy',
                    'params' => array(
                        'criteria' => array('sexo' => 'M', 'situacao' => 'A'),
                        'orderBy' => array('nome' => 'ASC')
                    ),
                ),
                'display_empty_item' => true,
                'empty_item_label' => 'Informe o nome do pai, CPF, ou RG da pessoa',
					'label_generator' => function($em) {
						$label = '';
						if ($em->getNome()){
							$label .=  $em->getNome();
						}
						if ($em->getCpf()){
							$label .= ' - CPF (' . $em->getCpf() . ')';
						}
						/*
                        if ($em->getRg()){
                            $label .= ' - ' . $em->getRg();
                        }*/

						return $label;

					},
            ),
        ));

		// $this->add(array(
		// 	'name' => 'cep',
		// 	'attributes' => array(
		// 		'type' => 'text',
		// 		'class' => 'form-control cep',
		// 		'style' => 'width:100px',
  //               'required' => 'required'
		// 	),
		// 	'options' => array(
		// 		'label' => 'CEP:'
		// 	),
		// ));

		// Endereco Externo		
		// Instanciando o Fieldset programaticamente

        //$endExterno = new \Usuario\Form\EnderecoExterno($em);
        //$endExterno->setUseAsBaseFieldset(false);
        //$this->add($endExterno);
        //$enderecoExternoFieldset = new EnderecoExternoFieldset($objectManager);
        $enderecoExternoFieldset = new EnderecoExternoFieldset($em);
        $enderecoExternoFieldset->setLabel('Endereço');
        $enderecoExternoFieldset->setName('enderecoExterno');
        $enderecoExternoFieldset->setUseAsBaseFieldset(false);
        $this->add($enderecoExternoFieldset);

		$documentoFieldset = new DocumentoFieldset($em);
		$documentoFieldset->setLabel('Documento');
        $documentoFieldset->setName('documento');
        $documentoFieldset->setUseAsBaseFieldset(false);
        $this->add($documentoFieldset);


        $telefoneFieldset = new TelefoneFieldset($objectManager);
//        $telefoneFieldset->setLabel('Telefones');
//        $telefoneFieldset->setName('telefones');
//        $telefoneFieldset->setUseAsBaseFieldset(false);
        $this->add(array(
            'type'    => 'Zend\Form\Element\Collection',
            'name' => 'telefones',
            'options' => array(
                'label' => 'Telefones:',
                'count'           => 2,
                'target_element' => $telefoneFieldset
            ),
            'attributes' => array(
                'type'    => 'Zend\Form\Element\Collection',
            )
        ));


        // Set the validation group so that we don't care about city
//        $this->setValidationGroup(array(
//            'csrf', // assume we added a CSRF element
//            'user' => array(
//                'name'
//            )
//        ));


//        $this->setValidationGroup(array(
//            'documento',
//        ));

		// $this->add(array(
		// 	'name' => 'tipoLogradouro',
		// 	'attributes' => array(
		// 		'type' => 'DoctrineModule\Form\Element\ObjectSelect',
		// 		'class' => 'form-control chosen-select tipoLogradouro',
		// 		'style' => 'height:100px;',
  //               'required' => 'required'
		// 	),
		// 	'type' => 'DoctrineModule\Form\Element\ObjectSelect',
		// 	'options' => array(
		// 		'label' => 'Tipo de Logradouro:',
		// 		'empty_option' => 'Selecione',
		// 		'object_manager' => $em,
		// 		'target_class' => 'Core\Entity\TipoLogradouro',
		// 		'property' => 'descricao',
		// 		'find_method' => array(
		// 			'name' => 'findBy',
		// 			'params' => array(
		// 				'criteria' => array(),
		// 				'orderBy' => array('descricao' => 'ASC')
		// 			)
		// 		)
		// 	),
		// ));

		// $this->add(array(
		// 	'name' => 'logradouro',
		// 	'attributes' => array(
		// 		'type' => 'text',
		// 		'class' => 'form-control logradouro',		
		// 		'placeholder' => 'Nome da Rua / Logradouro'		
		// 	),
		// 	'options' => array(
		// 		'label' => 'Logradouro:',
		// 		'object_manager' => $em,
		// 		'target_class' => 'Usuario\Entity\EnderecoExterno',
		// 		'property' => 'logradouro'
		// 	),
		// ));

		// $this->add(array(
		// 	'name' => 'numero',
		// 	'attributes' => array(
		// 		'type' => 'text',
		// 		'class' => 'form-control numero',		
		// 		'placeholder' => 'Número'		
		// 	),
		// 	'options' => array(
		// 		'label' => 'Número:'
		// 	),
		// ));

		// $this->add(array(
		// 	'name' => 'letra',
		// 	'attributes' => array(
		// 		'type' => 'text',
		// 		'class' => 'form-control letra',
		// 		'placeholder' => 'Letra'		
		// 	),
		// 	'options' => array(
		// 		'label' => 'Letra:'
		// 	),
		// ));

		// $this->add(array(
		// 	'name' => 'complemento',
		// 	'attributes' => array(
		// 		'type' => 'text',
		// 		'class' => 'form-control complemento',
		// 		'placeholder' => 'Informe o Complemento'
		// 	),
		// 	'options' => array(
		// 		'label' => 'Complemento:',							
		// 	),
		// ));

		// $this->add(array(
		// 	'name' => 'cidade',
		// 	'attributes' => array(
		// 		'type' => 'text',
		// 		'class' => 'form-control cidade',		
		// 		'placeholder' => 'Informe a Cidade'		
		// 	),
		// 	'options' => array(
		// 		'label' => 'Cidade:'
		// 	),
		// ));

		// $this->add(array(
		// 	'name' => 'bairro',
		// 	'attributes' => array(
		// 		'type' => 'text',
		// 		'class' => 'form-control bairro',
		// 		'placeholder' => 'Informe o Bairro'		
		// 	),
		// 	'options' => array(
		// 		'label' => 'Bairro:'
		// 	),
		// ));

		// $this->add(array(
		// 	'name' => 'uf',
		// 	'attributes' => array(
		// 		'type' => 'DoctrineModule\Form\Element\ObjectSelect',
		// 		'class' => 'form-control chosen-select uf',
		// 		'style' => 'height:100px;',
  //               'required' => 'required'
		// 	),
		// 	'type' => 'DoctrineModule\Form\Element\ObjectSelect',
		// 	'options' => array(
		// 		'label' => 'Estado:',
		// 		'empty_option' => 'Selecione',
		// 		'object_manager' => $em,
		// 		'target_class' => 'Core\Entity\Uf',
		// 		'property' => 'nome'				
		// 	),
		// ));

		$this->add(array(
			'name' => 'dataUniao',
			'attributes' => array(
				'type' => 'text',
				'class' => 'form-control'
			),
			'options' => array(
				'label' => 'Data de União'
			),
		));

		$this->add(array(
			'name' => 'dataObito',
			'attributes' => array(
				'type' => 'text',
				'class' => 'form-control'
			),
			'options' => array(
				'label' => 'Data do Obito'
			),
		));

		$this->add(array(
            'name' => 'nacionalidade',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'type' => 'Zend\Form\Element\Select',
                'class' => 'form-control nacionalidade'
            ),
            'options' => array(
                'empty_option' => 'Selecione',
                'label' => 'Nacionalidade:',
                'value_options' => array(
                    '1' => 'Brasileiro',
                    '2' => 'Naturalizado brasileiro',
                    '3' => 'Estrangeiro'
                ),
            ),
		));

		$this->add(array(
			'name' => 'dataChegadaBrasil',
			'attributes' => array(
				'type' => 'text',
				'class' => 'form-control'
			),
			'options' => array(
				'label' => 'Data de chegada ao Brasil'
			),
		));

		$this->add(array(
			'name' => 'ultimaEmpresa',
			'attributes' => array(
				'type' => 'text',
				'class' => 'form-control'
			),
			'options' => array(
				'label' => 'Última Empresa'
			),
		));

		$this->add(array(
			'name' => 'nomeMae',
			'attributes' => array(
				'type' => 'text',
				'class' => 'form-control'
			),
			'options' => array(
				'label' => 'Nome da Mãe'
			),
		));

		$this->add(array(
			'name' => 'nomeResponsavel',
			'attributes' => array(
				'type' => 'text',
				'class' => 'form-control'
			),
			'options' => array(
				'label' => 'Nome do Responsável'
			),
		));

		$this->add(array(
			'name' => 'justificativaProvisorio',
			'attributes' => array(
				'type' => 'text',
				'class' => 'form-control'
			),
			'options' => array(
				'label' => 'Justificativa Provisorio'
			),
		));

		$this->add(array(
			'name' => 'cpf',
			'attributes' => array(
				'type' => 'text',
				'class' => 'form-control cpf',
				'pattern' => "\d{3}\.\d{3}\.\d{3}-\d{2}",
				'title' => "Digite o CPF no formato nnn.nnn.nnn-nn"
			),
			'options' => array(
				'label' => 'CPF <small>nnn.nnn.nnn-nn</small>'
			),
		));


		$this->add(array(
			'name' => 'municipioNascimento',
            'attributes' => array(
                'type' => 'hidden',
                'class' => 'municipioNascimento',
//                'title' => 'Digite o nome do munícipio',
//                'placeholder' => 'Digite o Nome do Munícipio'
            ),
            'options' => array(
                'label' => 'Municipio de Nascimento:',
            ),
		));

		$this->add(array(
			'name' => 'idesco',
			'attributes' => array(
				'type' => 'Zend\Form\Element\Select',
				'class' => 'form-control'
			),
			'options' => array(
				'label' => 'Escolaridade'
			),
		));

		$this->add(array(
			'name' => 'ideciv',
			'attributes' => array(
				'type' => 'Zend\Form\Element\Select',
				'class' => 'form-control'
			),
			'options' => array(
				'label' => 'Estado Civil'
			),
		));

		$this->add(array(
			'name' => 'idocup',
			'attributes' => array(
				'type' => 'text',
				'class' => 'form-control'
			),
			'options' => array(
				'label' => 'Ocupação'
			),
		));

		$this->add(array(
			'name' => 'refCodReligiao',
			'attributes' => array(
				'type' => 'Zend\Form\Element\Select',
				'class' => 'form-control'
			),
			'options' => array(
				'label' => 'Religião'
			),
		));

		$this->add(array(
			'name' => 'url',
			'attributes' => array(
				'type' => 'text',
				'class' => 'form-control'
			),
			'options' => array(
				'label' => 'Site',
			),
		));

		$this->add(array(
			'name' => 'email',
			'attributes' => array(
				'type' => 'email',
				'class' => 'form-control'
			),
			'options' => array(
				'label' => 'Email',
			),
		));

        // $this->add(array(
        //     'name' => 'apartamento',
        //     'attributes' => array(
        //         'type' => 'text',
        //         'class' => 'form-control',
        //         'placeholder' => 'Nº'
        //     ),
        //     'options' => array(
        //         'label' => 'Número Apartamento'
        //     )
        // ));

        // $this->add(array(
        //     'name' => 'bloco',
        //     'attributes' => array(
        //         'type' => 'text',
        //         'class' => 'form-control',
        //         'placeholder' => 'Informe o Bloco'
        //     ),
        //     'options' => array(
        //         'label' => 'Bloco'
        //     )
        // ));

        // $this->add(array(
        // 	'name' => 'andar', 
        // 	'attributes' => array(
        // 		'type' => 'text',
        // 		'class' => 'form-control',
        // 		'placeholder' => 'Nº'
        // 	),
        // 	'options' => array(
        // 		'label' => 'Andar'
        // 	)
        // ));

  //       $this->add(array(
		// 	'name' => 'zonaLocalizacao',
		// 	'attributes' => array(				
		// 		'value' => '1',
		// 		'class' => 'form-control chosen-select',
		// 		'required' => 'required'
		// 	),
		// 	'type' => 'Zend\Form\Element\Select',
		// 	'options' => array(
		// 		'label' => 'Zona Localização',
		// 		'value_options' => array(
		// 			'1'  => 'Urbana',
  //    				'2'  => 'Rural',     				
		// 		),
		// 	),
		// ));


        $this->add(array(
            'name' => 'foto',
            'attributes' => array(
                'type' => 'file',
                'class' => 'form-control'
            ),
            'options' => array(
                'label' => 'Foto',
            ),
        ));


        $this->add(array(
            'name' => 'paisEstrangeiro',
            'attributes' => array(
                'type' => 'DoctrineModule\Form\Element\ObjectSelect',
                'class' => 'form-control chosen-select',
                'style' => 'height:100px;',
                'id' => 'paisEstrangeiro'
            ),
            'type' => 'DoctrineModule\Form\Element\ObjectSelect',
            'options' => array(
                'label' => 'Pais Estrangeiro:',
                //'empty_option' => 'Selecione',
                'allow_empty' => true,
                'continue_if_empty' => false,
                'object_manager' => $em,
                'target_class' => 'Core\Entity\Pais',
                'property' => 'nome',
                'find_method' => array(
                    'name' => 'findBy',
                    'params' => array(
                        'criteria' => array(),
                        'orderBy' => array('nome' => 'ASC')
                    ),
                ),
                'display_empty_item' => true,
                'empty_item_label'   => 'Selecione',
            ),
        ));



		$this->add(array(
			'name' => 'submit',
			'attributes' => array(
				'type' => 'submit',
				'value' => 'Salvar',
				'id' => 'submitbutton',
				'class' => 'btn btn-lg btn-primary',
			),
		));
	}


}