<?php

/*
 * This file is part of the Symfony2 PhoneNumberBundle.
 *
 * (c) University of Cambridge
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Misd\PhoneNumberBundle\Serializer\Handler;

use JMS\Serializer\DeserializationContext;
use JMS\Serializer\JsonDeserializationVisitor;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Visitor\DeserializationVisitorInterface;
use JMS\Serializer\Visitor\SerializationVisitorInterface;
use JMS\Serializer\XmlDeserializationVisitor;
use libphonenumber\PhoneNumber;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;
use SimpleXMLElement;

/**
 * Phone number serialization handler.
 */
class PhoneNumberHandler
{
    /**
     * Phone number utility.
     *
     * @var PhoneNumberUtil
     */
    private $phoneNumberUtil;

    /**
     * Constructor.
     *
     * @param PhoneNumberUtil $phoneNumberUtil Phone number utility.
     */
    public function __construct(PhoneNumberUtil $phoneNumberUtil)
    {
        $this->phoneNumberUtil = $phoneNumberUtil;
    }

    /**
     * Serialize a phone number.
     *
     * @param SerializationVisitorInterface $visitor     Serialization visitor.
     * @param PhoneNumber                   $phoneNumber Phone number.
     * @param array                         $type        Type.
     * @param SerializationContext          $context     Context.
     *
     * @return mixed Serialized phone number.
     */
    public function serializePhoneNumber(SerializationVisitorInterface $visitor, PhoneNumber $phoneNumber, array $type, SerializationContext $context)
    {
        $formatted = $this->phoneNumberUtil->format($phoneNumber, PhoneNumberFormat::E164);

        return $visitor->visitString($formatted, $type);
    }

    /**
     * Deserialize a phone number from JSON.
     *
     * @param DeserializationVisitorInterface $visitor Deserialization visitor.
     * @param string|null                     $data    Data.
     * @param array                           $type    Type.
     *
     * @return PhoneNumber|null Phone number.
     */
    public function deserializePhoneNumberFromJson(DeserializationVisitorInterface $visitor, $data, array $type)
    {
        if (null === $data) {
            return null;
        }

        return $this->phoneNumberUtil->parse($data, PhoneNumberUtil::UNKNOWN_REGION);
    }

    /**
     * Deserialize a phone number from XML.
     *
     * @param DeserializationVisitorInterface $visitor Deserialization visitor.
     * @param SimpleXMLElement                $data    Data.
     * @param array                           $type    Type.
     *
     * @return PhoneNumber|null Phone number.
     */
    public function deserializePhoneNumberFromXml(DeserializationVisitorInterface $visitor, $data, array $type)
    {
        $attributes = $data->attributes();
        if (
            (isset($attributes['nil'][0]) && (string) $attributes['nil'][0] === 'true') ||
            (isset($attributes['xsi:nil'][0]) && (string) $attributes['xsi:nil'][0] === 'true')
        ) {
            return null;
        }

        return $this->phoneNumberUtil->parse($data, PhoneNumberUtil::UNKNOWN_REGION);
    }
}
