<?php

namespace Yeah\Fw\Toolbox;
/**
 * Handles PHP errors and exceptions in a safe way
 */
class ErrorHandler {

    const TYPE_EXCEPTION = 0;
    const TYPE_ERROR = 1;

    private $handled = false;

    /**
     * 
     * @param int $error_reporting specified error reporting level
     * @param bool $display_errors Should php display errors on screen
     */
    public function __construct($error_reporting = E_ALL, $display_errors = 1) {
        $this->handled = false;

        ini_set('display_errors', $display_errors);
        error_reporting($error_reporting);

        set_error_handler(array($this, 'errorHandler'), $error_reporting);
        set_exception_handler(array($this, 'exceptionHandler'));
        register_shutdown_function(array($this, 'shutdownHandler'));
    }

    /**
     * 
     * @param int $errno Error number
     * @param string $errstr Error message
     * @param string $errfile Filepath where the error occured
     * @param string $errline Line in a file where error occured
     */
    public function errorHandler($errno, $errstr, $errfile, $errline, $errcontext) {
        $this->handled = true;
        if($errno & error_reporting()) {
            $this->render(array(
                'title' => 'Yeah! FW Error Handler',
                'message' => $errstr,
                'file' => $errfile,
                'line' => $errline,
                //'trace' => $errcontext,
                'type' => ErrorHandler::TYPE_ERROR
            ));
            die();
        }
    }

    /**
     * Handler for exceptions. Ignored on redirect exception
     * 
     * @param \Exception $e Caught exception
     */
    public function exceptionHandler(\Exception $e) {
        if($e->getCode() == '302') {
            return;
        }
        $this->handled = true;
        $this->render(array(
            'title' => 'Yeah! FW Exception Handler',
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTrace(),
            'type' => ErrorHandler::TYPE_EXCEPTION
        ));
        die();
    }

    /**
     * Last resort error checking. Uset for E_ERROR E_CORE error types which are
     * not cought by error handler
     */
    public function shutdownHandler() {
        $error = error_get_last();
        if(!$this->handled && $error && ($error['type'] & error_reporting())) {
            $this->errorHandler($error['type'], $error['message'], $error['file'], $error['line'], null);
        }
    }

    /**
     * Renders information on screen when error occures
     * 
     * @param array $options Error parameters
     */
    public function render($options) {
        http_response_code(500);
        $this->renderHtml($options);
    }

    public function renderHtml($options) {
        ?>
        <html>
            <head>
                <style>
                    #content {
                        padding: 10px;
                        background: #da5c00;
                        border-radius: 5px;
                    }
                    #message {
                        font-weight: bold;
                        font-size: 16pt;
                    }
                    #file {
                        color: #333333;
                        font-size: 16pt;
                    }
                    #trace {
                        padding-left: 10px;
                        color: white;
                    }
                </style>
            </head>
            <body>
                <h1 id="title"><?php echo $options['title']; ?></h1>
                <div id="content">
                    <div id="message">
                        <?php echo $options['message'] ?>
                    </div>
                    <div id="file">
                        <?php echo $options['file'] . ' at line ' . $options['line'] ?>
                    </div>
                    <?php if($options['type'] == ErrorHandler::TYPE_EXCEPTION) { ?>
                        <div id="trace">

                            <h2>Trace:</h2>
                            <?php
                            echo $this->print_r2($options['trace']);
                            ?>
                        </div>
                    <?php } ?>
                </div>
            </body>
        </html>
        <?php
    }

    public function print_r2($array) {
        return '<pre>' . print_r($array, true) . '</pre>';
    }
}
