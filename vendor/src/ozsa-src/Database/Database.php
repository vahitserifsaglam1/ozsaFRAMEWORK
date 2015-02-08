<?php





  class Database

  {

      const FETCH_OBJ = 5;

      const FETCH_ASSOC = 2;

      const FETCH_NUM = 3;

      const FETCH_BOTH = 4;

      protected $selectedTable;

      protected $adapter;

      protected $limit;

      protected $set;

      protected $get;

      protected $select = ['*'];

      protected $where;

      protected $like;

      protected $join;

      public function __construct()
      {
          $this->adapter = \Desing\Single::make( 'Adapter\Adapter','Database' );

          $this->adapter->addAdapter(\Desing\Single::make('\Database\Connector\Connector'));

          $this->adapter->addAdapter(\Desing\Single::make('\Database\Finder\tableFinder',$this->adapter->Connector));

          $this->adapter->alLAdaptersBoot();
      }


      public static function boot()
      {


         return new static();


      }

      public function table( $tableName = '' )

      {


           if(method_exists($this->adapter->tableFinder,'find') )
           {

               if($this->adapter->tableFinder->find( $tableName )){

                   $this->selectedTable = $tableName;


               }else{

                   throw new Database\Exceptions\QueryExceptions\unsuccessfullFindTableException( sprintf(" %s tablosu veritabanınız da bulunamadı",$tableName));

               }
           }else{

               throw new Database\Exceptions\MethodExceptions\undefinedMethodException( sprintf(" %s sınıfında yapmış olduğunuz %s methodu çağırma işlevi başarısız oldu",$this->adapter->tableFinder->getName(),'find'));

           }


           return $this;
      }

      public function addSet( $name,$value )
      {

          if( !isset($this->set[$this->selectedTable][$name]) )

          {

              $this->set[$this->selectedTable][$name] = $value;

          }

      }

      public function addGet( $name )
      {

          if( !isset( $this->get[$this->selectedTable][ $name ]) )
          {

               $this->get[$this->selectedTable][ $name ];

          }

      }

      public function setArray( Array $array = [] )
      {

          foreach ( $array as $key => $value )
          {

              $this->addSet( $key, $value );

          }

          return $this;

      }

      public function getArray( Array $array = [] )
      {

          foreach ( $array as $key )
          {
              $this->addGet( $key );
          }

          return $this;

      }

      public function select( $select )
      {

          $this->select[$this->selectedTable] = $select;

          return $this;
      }

      public function like(  $array = '' )
      {

          $this->like[$this->selectedTable]=$array;

          return $this;

      }

      public function join( Array $join = [] )
      {

          $this->join[$this->selectedTable] = $join;

          return $this;

      }

      public function where( Array $where = [] )
      {

          $this->where[$this->selectedTable] = $where;

          return $this;

      }



      public function find( Array $int = array() )

      {



            $this->limit[$this->selectedTable] = $int;

           return $this;

      }

      public function mixer(array $array,$end)
      {
          $s = "";

              foreach($array as $key => $value)
              {
                  $s .= $key.'='. "'$value'".$end;
              }


          return rtrim($s,$end);


      }

      /**
       * @param $array
       * @return string
       */
      public function wherer($array)
      {
          $s = "";
          foreach ( $array as $whereKey => $whereValue)
          {
              $s .= $whereKey.'='."'$whereValue' AND";
          }
          return rtrim($s," AND");
      }

      /**
       * @param array $array
       * @return string
       */
      public function liker(array $array)
      {

          foreach (  $array as $likeKey => $likeValue)
          {
              $like = $likeKey.' LIKE '.$likeValue.' ';
          }
          return $like;

      }

      /**
       * @param $join
       * @return string
       */
      public function joiner($join)
      {
          foreach($join as $joinKey => $joinVal)
          {
              $val = $joinKey.' '.$joinVal[0].' ON '.$joinVal[0].'.'.$joinVal[1].' = '.$this->selectedTable.'.'.$joinVal[2];
          }

          return $val;
      }

      /**
       * @param $limit
       * @return string
       */
      public function limiter($limit)
      {
          $limitbaslangic = $limit[0];

           $return = $limitbaslangic;

          if(isset($limit[1]))
          {
              $limitson = $limit[1];
              $return .= ','.$limitson.' ';
          }

          return $return;



      }

      /**
       * @param array $select
       * @return string
       */
      public function selecter( $select)
      {

          $s = '';
          if (is_array($select)) {
              foreach ($select as $selectKey) {
                  $s .= $selectKey . ',';
              }
              return rtrim($s, ',');
          } else {
              return "*";
          }

      }


      public function create()
      {
          $table = $this->selectedTable;

          $msg = ' INSERT INTO '.$this->selectedTable.' SET '.$this->wherer($this->set[$table]);

          return $this->que( $msg, false);

      }

      /**
       * @return PDOStatement
       */
      public function update()
      {
          $table = $this->selectedTable;
          $where = $this->where[$table];

          $msg = ' UPDATE '.$table.' SET '.$this->mixer($this->set[$table],', ').' WHERE '.$this->wherer($where);
          return $this->que( $msg, false);
      }

      /**
       * @return PDOStatement
       */
      public function delete()
      {
          $table = $this->selectedTable;
          $where = $this->where[$table];
          $msg = 'DELETE FROM '.$table.' WHERE '.$this->wherer($where);
          return $this->que( $msg, false);
      }

      /**
       * @return $this
       */
      public function read()
      {
          $table = $this->selectedTable;
          $where = $this->where[$table];
          $like  = $this->like[$table];
          $join  = $this->join[$table];
          $limit = $this->limit[$table];



          //where baslangic

          if(is_array($where))
          {
              $where = $this->wherer($where);
          }
          // where son

          // like baslangic
          if(is_array($like))
          {
              $like =   $this->liker($like);
          }
          // like son

          //join baslangic

          if(is_array($join))
          {
              $join = $this->joiner($join);
          }

          //join son

          //select baslangic

          $select = $this->selecter($this->get[$table]);

          //select son

          //limit başlangıç

          $limit =  $this->limiter($limit);



          //limit son

          $msg = 'SELECT '.$this->selecter($this->get[$table]).' FROM '.$this->selectedTable.' ';

          if ( isset($join) && is_string($join) )
          {
              $msg .= $join;
          }

          if ( isset ($where) && is_string($where) )
          {
              $msg .= ' WHERE '.$where;
          }

          if( isset($like) && is_string($like) )
          {
              if( isset( $where ) && is_string( $where )) $msg .= ' AND '.$like;
              else $msg .= ' WHERE '.$like;
          }

          if ( isset($limit ) && !is_array($limit) )
          {
              $msg .= ' LIMIT '.$limit;
          }

          return $this->que( $msg, true);

      }

      public function que($msg ,$fetch = false)
      {



           $query = $this->adapter->Connector->query($msg);

          if($query)
          {
              if( $fetch  )
              {

                  $query = $query->fetchAll(static::FETCH_OBJ);

              }


          }else{
              throw new Database\Exceptions\QueryExceptions\unsuccessfulQueryException( 'Sorgu başarısız',$msg);
              return false;
          }

          return  $query;




      }


      public function flush()
      {
          $this->set = array();
          $this->get = array();
          $this->mixed = array();
          $this->columns = array();
          $this->tableNames = array();
          $this->where = array();
          $this->join = array();
          $this->limit = array();
          $this->like = array();
          return null;
      }


      public function __call( $name, $params )
      {
          $ac = substr($name,0,3);


          if($ac == 'Get') {
              $this->addGet(str_replace("Get","",$name));
          }
          elseif($ac == 'Set') {
              $this->addSet($params[0],str_replace("Set","",$name));
          }
          else{
              return call_user_func_array(array( $this->adapter->Connector,$name),$params);
          }



      }




  }