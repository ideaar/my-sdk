<?php
	/**
	 *在已知XML文档的结构情况下，通过DOMDocument XPath将XML转化成数组
	 */

	/**
	 *通过指定的XPath和DOM对象查找子节点信息
	 *@param string $path xpath格式路径(example: '/Location/CountryRegion', './State') 具体参考：http://www.w3school.com.cn/xpath/
	 *@param DOMNode $DOMNode
	 *@param DOMXpath $xpath
	 *@return DOMNodeList|[] $children 返回一个空数组或者一个DOMNodeList对象
	 */
	function queryChildren($path, $DOMNode, $xpath){
		$children = array();
		if ( $DOMNode->hasChildNodes() ) {
			$children = $xpath->query($path, $DOMNode);
		}

		return $children;
	}

	//XML样例
	$str = <<<XML
<Location>
	<CountryRegion Code="1" Name="中国">
		<State Code="11" Name="北京">
			<city name='beijing' />
			<city name='zhaoyang' />
		</State>
	</CountryRegion>
</Location>
XML;

	//新建DOMDocument 对象
	$doc = new DOMDocument('1.0', 'utf-8');
	$doc->loadXML($str);

	//新建XPath对象
	$xpath = new DOMXPath($doc);

	//通过XPath查询
	$query = '/Location/CountryRegion';
	$countries = $xpath->query($query);

	//循环转化
	$data = array();
	foreach ($countries as $country) {
		$tempCountry = array();
		$tempCountry['name'] = $country->getAttribute('Name');
		$tempCountry['code'] = $country->getAttribute('Code');

		$states = queryChildren('./State', $country, $xpath);
		if ( !empty($states) ) {
			foreach ( $states as $state ) {
				$tempState = array();
				$tempState = [
					'name' => $state->getAttribute('Name'),
					'code' => $state->getAttribute('Code'),
				];
				$cities = queryChildren('./city', $state, $xpath);
				if ( !empty($cities) ) {
					foreach ( $cities as $city ) {
						$tempCity = array();
						$tempCity = [
							'name' => $city->getAttribute('name'),
						];

						$tempState['child'][] = $tempCity;
					}
				}

				$tempCountry['child'][] = $tempState;
			}
		}

		$data[] = $tempCountry;
	}
	var_dump($data[0]['child'][0]);exit;

