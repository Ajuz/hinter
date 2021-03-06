<?php
    namespace Maradik\Hinter\Api;    
    
    use Maradik\Testing\BaseData;
    use Maradik\Testing\BaseRepository; 
    use Maradik\Testing\QuestionData; 
    use Maradik\Testing\QuestionRepository;
    use Maradik\Hinter\Core\HttpResponseCode;
    use Maradik\Hinter\Core\RepositoryFactory;  
    use Maradik\Hinter\Core\IResource;    
    use Maradik\User\UserCurrent;
    
    abstract class MainQuestionController extends ResourceController implements IResource
    {
        public function __construct(RepositoryFactory $repositoryFactory, UserCurrent $user)
        {
            parent::__construct($repositoryFactory, $repositoryFactory->getMainQuestionRepository(), $user);
        }          
        
        /**
         * @param BaseData $entity         
         * @return array
         */        
        protected function packEntity(BaseData $entity)    
        {
            if (!($entity instanceof \Maradik\Testing\QuestionData)) {
                throw new \InvalidArgumentException(
                    'Неверный тип параметра $entity: ожидается \Maradik\Testing\QuestionData, получен '
                  . get_class($entity)
                );       
            }               
            
            $ret = $entity->jsonSerialize(); //TODO Переделать в JSON!
            $ret['images'] = $this->getPackedImages($entity->id);
            return $ret;            
        }
        
        /**
         * @param array $data         
         * @return BaseData
         */        
        protected function unpackEntity(array $data)
        {                          
            $ret = QuestionData::createFromJson($data); //TODO Переделать из JSON!  
            $ret->userId     = $this->user->data()->id;
            $ret->createDate = time();

            if (!$this->user->isAdmin()) {
                $ret->active = false;    
            }            
            
            return $ret;                        
        }                      
    }    