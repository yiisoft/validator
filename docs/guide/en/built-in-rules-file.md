# File

`File` checks that a value is a file and can validate its extension, MIME type, and size.

Supported values:

- string file paths;
- `SplFileInfo` instances;
- [PSR-7] `UploadedFileInterface` instances.

Use `Each` for multiple files:

```php
use Yiisoft\Validator\Rule\Each;
use Yiisoft\Validator\Rule\File;

$rules = [
    'attachments' => new Each(new File(extensions: ['pdf', 'txt'])),
];
```

For example, with a PSR-7 request:

```php
use Yiisoft\Validator\Rule\Each;
use Yiisoft\Validator\Rule\File;
use Yiisoft\Validator\Validator;

$uploadedFiles = $request->getUploadedFiles()['attachments'] ?? [];

$result = (new Validator())->validate(
    ['attachments' => $uploadedFiles],
    ['attachments' => new Each(new File(maxSize: 5_000_000))],
);
```

If your application works with native PHP `$_FILES` data directly, convert it to supported values such as PSR-7 uploaded
file objects before passing it to the validator.

## Uploaded Files

`File` handles PSR-7 upload error codes. `UPLOAD_ERR_NO_FILE` is treated as a missing value, so optional upload fields
can use `skipOnEmpty`:

```php
use Yiisoft\Validator\Rule\File;

$rule = new File(skipOnEmpty: true);
```

Other upload error codes fail validation with `uploadFailedMessage`.

The rule does not prove that arbitrary string paths or `SplFileInfo` values came from PHP's HTTP upload mechanism. Do
not pass user-submitted paths directly. Use PSR-7 uploaded file objects from a trusted request implementation, or perform
[upload provenance checks] before validating filesystem paths.

## MIME Types

For filesystem-backed files, MIME type validation uses PHP's file information facilities through
`mime_content_type()`. If the MIME type can't be determined, MIME validation fails.

For pathless PSR-7 uploads backed only by an in-memory stream, `File` doesn't trust client-provided media type by
default. If your application has already decided that the client metadata is acceptable for this field, enable it
explicitly:

```php
use Yiisoft\Validator\Rule\File;

$rule = new File(
    mimeTypes: ['text/plain'],
    trustClientMediaType: true,
);
```

This option should be used with care because the client can send any media type value.

## Size

For filesystem-backed uploads, size checks use the actual file size on disk. For pathless streams, size checks use the
PSR-7 upload size when available. If a size constraint is configured and the size can't be determined, validation fails.

`size` is mutually exclusive with `minSize` and `maxSize`. When both `minSize` and `maxSize` are set, `minSize` must be
less than or equal to `maxSize`.

## Request Body Streams

`File` doesn't validate generic request body streams such as data read from `php://input` for PUT requests. Convert such
input to a supported value first, or write a custom rule that validates your stream format and storage flow.

[PSR-7]: https://www.php-fig.org/psr/psr-7/
[upload provenance checks]: https://www.php.net/manual/en/features.file-upload.post-method.php
