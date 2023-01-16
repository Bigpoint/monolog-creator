# Changelog

# 1.* -> 2.*
- Minimal PHP compatibility was increased from PHP 5.3 to 8.1
- The `monolog/monolog` dependency was updated from `1.*` to `3.*`
- The `predis/predis` direct dependency was removed. If you want to use the redis handler, please require this library in your project now and assign the object to the MonologCreator Factory separately. For the details see the documentation [here](/README.md#redishandler-with-predispredis).
- The output of the `json` and `logstash` formatter are different now. Before the context fields where prefix with `ctxt_`. Now, it will be a new list named `context`. So `ctxt_test` will be `context.test`. Also the extra fields are in a similar list called `extra`.
