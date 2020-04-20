<?php
//require_once CLASSES_PATH . 'Test.php';

class Parser
{
    static public function Execute()
    {
        // дефолтный ответ
        $response = ['status' => 0, 'message' => 'NotFoundException'];
        // делим uri по &
        // не через $_SERVER["QUERY_STRING"] чтобы сразу разделить юрл на две части а не убирать
        // $_SERVER["QUERY_STRING"] через substr()
        $urlParamsCheck = array_values(array_filter(explode('?', $_SERVER['REQUEST_URI'])));

        $urlParams = [];
        // если есть QUERY_STRING то разбиваем ее заносим в массив параметров
        if (isset($urlParamsCheck[1]))
            $urlParams = self::parseQueryString($urlParamsCheck[1]);

        // дулим юрл на части без QUERY_STRING по слэшу
        $urlParts = array_values(array_filter(explode('/', $urlParamsCheck[0])));

        // проверяем класс
        if (!class_exists($urlParts[0]))
            return json_encode($response);
        else
            $className = ucfirst($urlParts[0]);

        // проверяем метод
        $class = new $className;
        if (!isset($urlParts[1]) || !method_exists($class, $urlParts[1]))
            return json_encode($response);
        else
            $methodName = $urlParts[1];

        try {
            // для получения необходимого количества параметров
            $r = new ReflectionMethod($className, $methodName);
            // собираем массив параметров
            $paramsArray = [];
            // кол-во оставшихся частей юрла кроме первых двух (класса и метода)
            for ($i = 0; $i < count($urlParts) - 2; $i++)
                $paramsArray[] = $urlParts[$i + 2];

            if (!empty($urlParams))
                $paramsArray[] = $urlParams;

            // если количество параметров меньше требуемого методом количества
            // или больше допустимого методом количества
            if (count($paramsArray) < $r->getNumberOfRequiredParameters()
                || count($paramsArray) > $r->getNumberOfParameters())
                return json_encode($response);

            $response['message'] = $class->$methodName(...$paramsArray);
            $response['status'] = 1;
        } catch (ReflectionException $e) {
            return json_encode($response);
        }

        return json_encode($response);
    }

    static public function parseQueryString($string)
    {
        $urlParamsStringSeparated = explode('&', $string);
        $urlParams = [];
        foreach ($urlParamsStringSeparated as $paramsPart) {
            $paramsValues = array_values(array_filter(explode('=', $paramsPart)));
            // скипаем непарсящиеся параметры
            if (!isset($paramsValues[0]) || !isset($paramsValues[1]))
                continue;
            $urlParams[$paramsValues[0]] = $paramsValues[1];
        }
        return $urlParams;
    }
}