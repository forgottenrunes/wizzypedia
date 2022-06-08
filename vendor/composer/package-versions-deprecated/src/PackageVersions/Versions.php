<?php

declare(strict_types=1);

namespace PackageVersions;

use Composer\InstalledVersions;
use OutOfBoundsException;

class_exists(InstalledVersions::class);

/**
 * This class is generated by composer/package-versions-deprecated, specifically by
 * @see \PackageVersions\Installer
 *
 * This file is overwritten at every run of `composer install` or `composer update`.
 *
 * @deprecated in favor of the Composer\InstalledVersions class provided by Composer 2. Require composer-runtime-api:^2 to ensure it is present.
 */
final class Versions
{
    /**
     * @deprecated please use {@see self::rootPackageName()} instead.
     *             This constant will be removed in version 2.0.0.
     */
    const ROOT_PACKAGE_NAME = 'mediawiki/core';

    /**
     * Array of all available composer packages.
     * Dont read this array from your calling code, but use the \PackageVersions\Versions::getVersion() method instead.
     *
     * @var array<string, string>
     * @internal
     */
    const VERSIONS          = array (
  'composer/semver' => '3.2.6@83e511e247de329283478496f7a1e114c9517506',
  'cssjanus/cssjanus' => 'v2.1.0@de7483c0805750a6462b372eab55d022d555df02',
  'guzzlehttp/guzzle' => '7.4.1@ee0a041b1760e6a53d2a39c8c34115adc2af2c79',
  'guzzlehttp/promises' => '1.5.1@fe752aedc9fd8fcca3fe7ad05d419d32998a06da',
  'guzzlehttp/psr7' => '2.2.2@a119247127ff95789a2d95c347cd74721fbedaa4',
  'justinrainbow/json-schema' => '5.2.11@2ab6744b7296ded80f8cc4f9509abbff393399aa',
  'liuggio/statsd-php-client' => 'v1.0.18@c42e6d6687b7b2d7683186ec7f4f03351cc3dbca',
  'monolog/monolog' => '2.2.0@1cb1cde8e8dd0f70cc0fe51354a59acad9302084',
  'oojs/oojs-ui' => 'v0.43.2@630d30f69cec9d64ef6d6bf0d1afed158bc62ffe',
  'pear/console_getopt' => 'v1.4.3@a41f8d3e668987609178c7c4a9fe48fecac53fa0',
  'pear/mail' => 'v1.4.1@9609ed5e42ac5b221dfd9af85de005c59d418ee7',
  'pear/mail_mime' => '1.10.11@d4fb9ce61201593d0f8c6db629c45e29c3409c14',
  'pear/net_smtp' => '1.10.0@51e5997b711fbd1e5a9a075634d4d682168537fa',
  'pear/net_socket' => 'v1.2.2@bbe6a12bb4f7059dba161f6ddd43f369c0ec8d09',
  'pear/net_url2' => 'v2.2.2@07fd055820dbf466ee3990abe96d0e40a8791f9d',
  'pear/pear-core-minimal' => 'v1.10.11@68d0d32ada737153b7e93b8d3c710ebe70ac867d',
  'pear/pear_exception' => 'v1.0.2@b14fbe2ddb0b9f94f5b24cf08783d599f776fff0',
  'pleonasm/bloom-filter' => '1.0.2@4a3292c9f83a778c44271bf4e4f6be1204b87f7b',
  'psr/container' => '1.1.1@8622567409010282b7aeebe4bb841fe98b58dcaf',
  'psr/http-client' => '1.0.1@2dfb5f6c5eff0e91e20e913f8c5452ed95b86621',
  'psr/http-factory' => '1.0.1@12ac7fcd07e5b077433f5f2bee95b3a771bf61be',
  'psr/http-message' => '1.0.1@f6561bf28d520154e4b0ec72be95418abe6d9363',
  'psr/log' => '1.1.4@d49695b909c3b7628b6289db5479a1c204601f11',
  'ralouphie/getallheaders' => '3.0.3@120b605dfeb996808c31b6477290a714d356e822',
  'symfony/deprecation-contracts' => 'v2.5.1@e8b495ea28c1d97b5e0c121748d6f9b53d075c66',
  'symfony/polyfill-php80' => 'v1.25.0@4407588e0d3f1f52efb65fbe92babe41f37fe50c',
  'symfony/yaml' => 'v5.4.3@e80f87d2c9495966768310fc531b487ce64237a2',
  'wikimedia/assert' => 'v0.5.1@27c983fddac1197dc37f6a7cec00fc02861529cd',
  'wikimedia/at-ease' => 'v2.1.0@e8ebaa7bb7c8a8395481a05f6dc4deaceab11c33',
  'wikimedia/base-convert' => 'v2.0.1@449f0d0237cf1e0e71faec90680c88d4af6e711d',
  'wikimedia/cdb' => '2.0.0@70c724f88faa74338c9918f5b999445a615593e8',
  'wikimedia/cldr-plural-rule-parser' => 'v2.0.0@83d78cb8018d5c0f66fd6d0efff6a8ae2de92d36',
  'wikimedia/common-passwords' => 'v0.3.0@5b51a88a27e17f485ba1295ee6916620686cd5a5',
  'wikimedia/composer-merge-plugin' => 'v2.0.1@8ca2ed8ab97c8ebce6b39d9943e9909bb4f18912',
  'wikimedia/dodo' => 'v0.4.0@47e8176b8fd04ea00034d91b524f272e3745604a',
  'wikimedia/html-formatter' => '3.0.1@f18622f3384b9b7fed185bff2a46594aec92fa47',
  'wikimedia/idle-dom' => 'v0.10.0@1d34b55cfe259fd26c541811581c2e1578b46727',
  'wikimedia/ip-set' => '3.0.0@4efe81f0ffb907a60778a72faf6ede17bb490081',
  'wikimedia/ip-utils' => '4.0.0@f52a68b95fd3aac91cde3bdbc8654e2faa2fba38',
  'wikimedia/less.php' => 'v3.1.0@a486d78b9bd16b72f237fc6093aa56d69ce8bd13',
  'wikimedia/minify' => '2.2.6@a8e4eb7a7a96b5fe4d64187d0c7390375c0041e4',
  'wikimedia/normalized-exception' => 'v1.0.1@ed9fc13d75f65c80dc1a95d4792658c97fd782e6',
  'wikimedia/object-factory' => 'v4.0.0@20d19657cfbedb1e7bd61c6696e8a16ade232e5c',
  'wikimedia/parsoid' => 'v0.15.0@aaf92473a7d3859ddb513de5672e42531ba086fd',
  'wikimedia/php-session-serializer' => 'v2.0.0@99e7e926f1b61f71623d517fe38d9eec8618c59d',
  'wikimedia/purtle' => 'v1.0.8@8f106f38ff811906853511a934d7f5e86dce3d20',
  'wikimedia/relpath' => '3.0.0@b237d203c820cd1000f2c5ecad25de9fa7165612',
  'wikimedia/remex-html' => '3.0.1@962d1b028d505f488b77ded093cacce9a0855f29',
  'wikimedia/request-timeout' => '1.2.0@e306a7cb1fb3a1ca3ce93992c56efb62b537c4bc',
  'wikimedia/running-stat' => 'v1.2.1@60eebada7cc64b7073d90e7d4bab00efaafa0ba9',
  'wikimedia/scoped-callback' => 'v3.0.0@0a480d9a9772634697c77598726cf24606597bd0',
  'wikimedia/services' => '2.0.1@5ef69a8a8b0d2ea115d08469bdab468f58fac820',
  'wikimedia/shellbox' => '3.0.0@6b5abcd4a467b27b5ba4fc28f7730ea5d01367a1',
  'wikimedia/timestamp' => 'v3.0.0@42ce5586d2189826e28ebcf4cedc96226b6d77e2',
  'wikimedia/utfnormal' => '3.0.2@e690d29487a6ee346bcf4cbf5a6fd89170fa2306',
  'wikimedia/wait-condition-loop' => 'v2.0.2@9bb0894e8c5195d43b2f2babbe4cc8f36bd5be0e',
  'wikimedia/wikipeg' => '2.0.6@9a92384ae11e1a3b7ecfe0feef1809b1af73889b',
  'wikimedia/wrappedstring' => 'v4.0.1@0d526868d92fa855c70a845336a777c63b30b400',
  'wikimedia/xmp-reader' => '0.8.1@4fc577e28e09eec165b64ed74ce878ba4ea45a35',
  'wikimedia/zest-css' => '2.0.2@423c867462801fda08a1c31009ec19d91a68b410',
  'zordius/lightncandy' => 'v1.2.6@b451f73e8b5c73e62e365997ba3c993a0376b72a',
  'composer/package-versions-deprecated' => '1.11.99.5@b4f54f74ef3453349c24a845d22392cd31e65f1d',
  'composer/pcre' => '1.0.1@67a32d7d6f9f560b726ab25a061b38ff3a80c560',
  'composer/spdx-licenses' => '1.5.5@de30328a7af8680efdc03e396aad24befd513200',
  'composer/xdebug-handler' => '2.0.5@9e36aeed4616366d2b690bdce11f71e9178c579a',
  'doctrine/cache' => '2.2.0@1ca8f21980e770095a31456042471a57bc4c68fb',
  'doctrine/dbal' => '3.1.5@187916074db413f4a070a721c9d30b420a9135dd',
  'doctrine/deprecations' => 'v0.5.3@9504165960a1f83cc1480e2be1dd0a0478561314',
  'doctrine/event-manager' => '1.1.1@41370af6a30faa9dc0368c4a6814d596e81aba7f',
  'doctrine/instantiator' => '1.4.1@10dcfce151b967d20fde1b34ae6640712c3891bc',
  'doctrine/sql-formatter' => '1.1.1@56070bebac6e77230ed7d306ad13528e60732871',
  'felixfbecker/advanced-json-rpc' => 'v3.2.1@b5f37dbff9a8ad360ca341f3240dc1c168b45447',
  'giorgiosironi/eris' => '0.10.0@d7cbea45ff7c7621d69589ae7f8a82f183673e69',
  'hamcrest/hamcrest-php' => 'v2.0.1@8c3d0a3f6af734494ad8f6fbbee0ba92422859f3',
  'johnkary/phpunit-speedtrap' => 'v4.0.0@5f9b160eac87e975f1c6ca9faee5125f0616fba3',
  'mediawiki/mediawiki-codesniffer' => 'v38.0.0@059db7ef17adf2fd1088c42a05e6736e5c2aab2a',
  'mediawiki/mediawiki-phan-config' => '0.11.0@e1891169976e0f8062a06c851687b32cf91b980e',
  'mediawiki/phan-taint-check-plugin' => '3.3.2@6d38c59222ede306773ec2baac8d78843478a360',
  'microsoft/tolerant-php-parser' => 'v0.1.1@6a965617cf484355048ac6d2d3de7b6ec93abb16',
  'myclabs/deep-copy' => '1.11.0@14daed4296fae74d9e3201d2c4925d1acb7aa614',
  'netresearch/jsonmapper' => 'v4.0.0@8bbc021a8edb2e4a7ea2f8ad4fa9ec9dce2fcb8d',
  'nikic/php-parser' => 'v4.14.0@34bea19b6e03d8153165d8f30bba4c3be86184c1',
  'nmred/kafka-php' => 'v0.1.5@317ad8c208684db8b9e6d2f5bf7f471e89a8b4eb',
  'phan/phan' => '5.2.0@eb59e65097dc8035fdaaa66db4b565585decceb0',
  'phar-io/manifest' => '2.0.3@97803eca37d319dfa7826cc2437fc020857acb53',
  'phar-io/version' => '3.2.1@4f7fd7836c6f332bb2933569e566a0d6c4cbed74',
  'php-parallel-lint/php-console-color' => 'v0.3@b6af326b2088f1ad3b264696c9fd590ec395b49e',
  'php-parallel-lint/php-console-highlighter' => 'v0.5@21bf002f077b177f056d8cb455c5ed573adfdbb8',
  'php-parallel-lint/php-parallel-lint' => 'v1.3.1@761f3806e30239b5fcd90a0a45d41dc2138de192',
  'phpdocumentor/reflection-common' => '2.2.0@1d01c49d4ed62f25aa84a747ad35d5a16924662b',
  'phpdocumentor/reflection-docblock' => '5.3.0@622548b623e81ca6d78b721c5e029f4ce664f170',
  'phpdocumentor/type-resolver' => '1.6.1@77a32518733312af16a44300404e945338981de3',
  'phpspec/prophecy' => 'v1.15.0@bbcd7380b0ebf3961ee21409db7b38bc31d69a13',
  'phpunit/php-code-coverage' => '7.0.15@819f92bba8b001d4363065928088de22f25a3a48',
  'phpunit/php-file-iterator' => '2.0.5@42c5ba5220e6904cbfe8b1a1bda7c0cfdc8c12f5',
  'phpunit/php-text-template' => '1.2.1@31f8b717e51d9a2afca6c9f046f5d69fc27c8686',
  'phpunit/php-timer' => '2.1.3@2454ae1765516d20c4ffe103d85a58a9a3bd5662',
  'phpunit/php-token-stream' => '4.0.4@a853a0e183b9db7eed023d7933a858fa1c8d25a3',
  'phpunit/phpunit' => '8.5.26@ef117c59fc4c54a979021b26d08a3373e386606d',
  'psy/psysh' => 'v0.11.5@c23686f9c48ca202710dbb967df8385a952a2daf',
  'sabre/event' => '5.1.4@d7da22897125d34d7eddf7977758191c06a74497',
  'sebastian/code-unit-reverse-lookup' => '1.0.2@1de8cd5c010cb153fcd68b8d0f64606f523f7619',
  'sebastian/comparator' => '3.0.3@1071dfcef776a57013124ff35e1fc41ccd294758',
  'sebastian/diff' => '3.0.3@14f72dd46eaf2f2293cbe79c93cc0bc43161a211',
  'sebastian/environment' => '4.2.4@d47bbbad83711771f167c72d4e3f25f7fcc1f8b0',
  'sebastian/exporter' => '3.1.4@0c32ea2e40dbf59de29f3b49bf375176ce7dd8db',
  'sebastian/global-state' => '3.0.2@de036ec91d55d2a9e0db2ba975b512cdb1c23921',
  'sebastian/object-enumerator' => '3.0.4@e67f6d32ebd0c749cf9d1dbd9f226c727043cdf2',
  'sebastian/object-reflector' => '1.1.2@9b8772b9cbd456ab45d4a598d2dd1a1bced6363d',
  'sebastian/recursion-context' => '3.0.1@367dcba38d6e1977be014dc4b22f47a484dac7fb',
  'sebastian/resource-operations' => '2.0.2@31d35ca87926450c44eae7e2611d45a7a65ea8b3',
  'sebastian/type' => '1.1.4@0150cfbc4495ed2df3872fb31b26781e4e077eb4',
  'sebastian/version' => '2.0.1@99732be0ddb3361e16ad77b68ba41efc8e979019',
  'seld/jsonlint' => '1.8.3@9ad6ce79c342fbd44df10ea95511a1b24dee5b57',
  'squizlabs/php_codesniffer' => '3.6.1@f268ca40d54617c6e06757f83f699775c9b3ff2e',
  'symfony/console' => 'v5.4.9@829d5d1bf60b2efeb0887b7436873becc71a45eb',
  'symfony/polyfill-intl-grapheme' => 'v1.26.0@433d05519ce6990bf3530fba6957499d327395c2',
  'symfony/polyfill-intl-normalizer' => 'v1.26.0@219aa369ceff116e673852dce47c3a41794c14bd',
  'symfony/polyfill-php73' => 'v1.26.0@e440d35fa0286f77fb45b79a03fedbeda9307e85',
  'symfony/service-contracts' => 'v2.5.1@24d9dc654b83e91aa59f9d167b131bc3b5bea24c',
  'symfony/string' => 'v5.4.9@985e6a9703ef5ce32ba617c9c7d97873bb7b2a99',
  'symfony/var-dumper' => 'v5.4.9@af52239a330fafd192c773795520dc2dd62b5657',
  'theseer/tokenizer' => '1.2.1@34a41e998c2183e22995f158c581e7b5e755ab9e',
  'tysonandre/var_representation_polyfill' => '0.1.1@0a942e74e18af5514749895507bc6ca7ab96399a',
  'webmozart/assert' => '1.11.0@11cb2199493b2f8a3b53e7f19068fc6aac760991',
  'wikimedia/testing-access-wrapper' => '2.0.0@280e613c76f481f2fe90e0ea22361080b6ab0d65',
  'wmde/hamcrest-html-matchers' => 'v1.0.0@f0f727f80bdb97831628329cfe508fce2a44028a',
  'symfony/polyfill-ctype' => '1.99@',
  'symfony/polyfill-mbstring' => '1.99@',
  'mediawiki/core' => '1.0.0+no-version-set@',
);

    private function __construct()
    {
    }

    /**
     * @psalm-pure
     *
     * @psalm-suppress ImpureMethodCall we know that {@see InstalledVersions} interaction does not
     *                                  cause any side effects here.
     */
    public static function rootPackageName() : string
    {
        if (!self::composer2ApiUsable()) {
            return self::ROOT_PACKAGE_NAME;
        }

        return InstalledVersions::getRootPackage()['name'];
    }

    /**
     * @throws OutOfBoundsException If a version cannot be located.
     *
     * @psalm-param key-of<self::VERSIONS> $packageName
     * @psalm-pure
     *
     * @psalm-suppress ImpureMethodCall we know that {@see InstalledVersions} interaction does not
     *                                  cause any side effects here.
     */
    public static function getVersion(string $packageName): string
    {
        if (self::composer2ApiUsable()) {
            return InstalledVersions::getPrettyVersion($packageName)
                . '@' . InstalledVersions::getReference($packageName);
        }

        if (isset(self::VERSIONS[$packageName])) {
            return self::VERSIONS[$packageName];
        }

        throw new OutOfBoundsException(
            'Required package "' . $packageName . '" is not installed: check your ./vendor/composer/installed.json and/or ./composer.lock files'
        );
    }

    private static function composer2ApiUsable(): bool
    {
        if (!class_exists(InstalledVersions::class, false)) {
            return false;
        }

        if (method_exists(InstalledVersions::class, 'getAllRawData')) {
            $rawData = InstalledVersions::getAllRawData();
            if (count($rawData) === 1 && count($rawData[0]) === 0) {
                return false;
            }
        } else {
            $rawData = InstalledVersions::getRawData();
            if ($rawData === null || $rawData === []) {
                return false;
            }
        }

        return true;
    }
}
