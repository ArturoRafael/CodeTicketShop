<!-- START_<?php echo e($route['id']); ?> -->
<?php if($route['title'] != ''): ?>## <?php echo e($route['title']); ?>

<?php else: ?>## <?php echo e($route['uri']); ?><?php endif; ?>
<?php if($route['authenticated']): ?>

<br><small style="padding: 1px 9px 2px;font-weight: bold;white-space: nowrap;color: #ffffff;-webkit-border-radius: 9px;-moz-border-radius: 9px;border-radius: 9px;background-color: #3a87ad;">Requires authentication</small><?php endif; ?>
<?php if($route['description']): ?>

<?php echo $route['description']; ?>

<?php endif; ?>

> Example request:

<?php $__currentLoopData = $settings['languages']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $language): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<?php echo $__env->make("apidoc::partials.example-requests.$language", \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>

<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

<?php if(in_array('GET',$route['methods']) || (isset($route['showresponse']) && $route['showresponse'])): ?>
<?php if(is_array($route['response'])): ?>
<?php $__currentLoopData = $route['response']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $response): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
> Example response (<?php echo e($response['status']); ?>):


```json
<?php if(is_object($response['content']) || is_array($response['content'])): ?>
<?php echo json_encode($response['content'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE); ?>

<?php else: ?>
<?php echo json_encode(json_decode($response['content']), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE); ?>

<?php endif; ?>
```
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php else: ?>
> Example response:

```json
<?php if(is_object($route['response']) || is_array($route['response'])): ?>
<?php echo json_encode($route['response'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE); ?>

<?php else: ?>
<?php echo json_encode(json_decode($route['response']), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE); ?>

<?php endif; ?>
```
<?php endif; ?>
<?php endif; ?>

### HTTP Request
<?php $__currentLoopData = $route['methods']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $method): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
`<?php echo e($method); ?> <?php echo e($route['uri']); ?>`

<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php if(count($route['bodyParameters'])): ?>
#### Body Parameters

Parameter | Type | Status | Description
--------- | ------- | ------- | ------- | -----------
<?php $__currentLoopData = $route['bodyParameters']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attribute => $parameter): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php echo e($attribute); ?> | <?php echo e($parameter['type']); ?> | <?php if($parameter['required']): ?> required <?php else: ?> optional <?php endif; ?> | <?php echo $parameter['description']; ?>

<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php endif; ?>
<?php if(count($route['queryParameters'])): ?>
#### Query Parameters

Parameter | Status | Description
--------- | ------- | ------- | -----------
<?php $__currentLoopData = $route['queryParameters']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attribute => $parameter): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php echo e($attribute); ?> | <?php if($parameter['required']): ?> required <?php else: ?> optional <?php endif; ?> | <?php echo $parameter['description']; ?>

<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php endif; ?>

<!-- END_<?php echo e($route['id']); ?> -->
