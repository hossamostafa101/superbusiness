<?php

namespace App\Logging;

use Monolog\Logger;
use Monolog\Formatter\LineFormatter;

class CustomizeFormatter
{
    public function __invoke(Logger $logger): void
    {
        foreach ($logger->getHandlers() as $handler) {
            $fmt = $handler->getFormatter();
            if ($fmt instanceof LineFormatter) {
                // قلّل “تضخيم” الرسالة
                $fmt->allowInlineLineBreaks(false);
                $fmt->ignoreEmptyContextAndExtra(true);
                $fmt->includeStacktraces(false);

                // لو متاحة في نسختك من Monolog 3:
                if (method_exists($fmt, 'setMaxNormalizeDepth')) {
                    $fmt->setMaxNormalizeDepth(2);          // عمق تطبيع صغير
                }
                if (method_exists($fmt, 'setMaxNormalizeItemCount')) {
                    $fmt->setMaxNormalizeItemCount(50);     // عدد عناصر قليل
                }
            }
        }
    }
}
