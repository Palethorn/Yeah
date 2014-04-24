<?php

namespace Yeah\Fw\Toolbox;

class ErrorHandler {

    const TYPE_EXCEPTION = 0;
    const TYPE_ERROR = 1;

    public function __construct() {
        set_error_handler(array($this, 'errorHandler'), E_ALL);
        set_exception_handler(array($this, 'exceptionHandler'));
    }

    public function errorHandler($errno, $errstr, $errfile, $errline, $errcontext) {
        $this->render(array(
            'title' => 'Yeah! FW Error Handler',
            'message' => $errstr,
            'file' => $errfile,
            'line' => $errline,
            'trace' => $errcontext,
            'type' => ErrorHandler::TYPE_ERROR
        ));
        die();
    }

    public function exceptionHandler(\Exception $e) {
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

    public function render($options) {
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
                            foreach ($options['trace'] as $line) {
                                echo $line['file'] . ' - ' . $line['function'] . ' at ' . $line['line'] . '<br>';
                            }
                            ?>
                        </div>
                    <?php } ?>
                </div>
            </body>
        </html>
        <?php
    }

}
