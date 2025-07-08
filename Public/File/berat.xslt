<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="2.0" xmlns:edefter="http://www.edefter.gov.tr" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:clitype="clitype" xmlns:def="http://www.fujitsu.com/xbrl/gl/ext/2005-04-01" xmlns:ds="http://www.w3.org/2000/09/xmldsig#" xmlns:fn="http://www.w3.org/2005/xpath-functions" xmlns:gl-bus="http://www.xbrl.org/int/gl/bus/2006-10-25" xmlns:gl-cor="http://www.xbrl.org/int/gl/cor/2006-10-25" xmlns:gl-gen="http://www.xbrl.org/int/gl/gen/2006-10-25" xmlns:iso4217="http://www.xbrl.org/2003/iso4217" xmlns:ix="http://www.xbrl.org/2008/inlineXBRL" xmlns:java="java" xmlns:link="http://www.xbrl.org/2003/linkbase" xmlns:plt="http://www.gib.gov.tr/int/gl/plt/2010-05-28" xmlns:sh="http://www.unece.org/cefact/namespaces/StandardBusinessDocumentHeader" xmlns:xades="http://uri.etsi.org/01903/v1.3.2#" xmlns:xbrldi="http://xbrl.org/2006/xbrldi" xmlns:xbrli="http://www.xbrl.org/2003/instance" xmlns:xl="http://www.xbrl.org/2003/XLink" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:xs="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" exclude-result-prefixes="clitype def ds fn gl-bus gl-cor gl-gen iso4217 ix java link plt sh xades xbrldi xbrli xl xlink xs xsi">
	<xsl:character-map name="myMap">
		<xsl:output-character character="&#128;" string=""/>
		<xsl:output-character character="&#129;" string=""/>
		<xsl:output-character character="&#130;" string=""/>
		<xsl:output-character character="&#131;" string=""/>
		<xsl:output-character character="&#132;" string=""/>
		<xsl:output-character character="&#133;" string=""/>
		<xsl:output-character character="&#134;" string=""/>
		<xsl:output-character character="&#135;" string=""/>
		<xsl:output-character character="&#136;" string=""/>
		<xsl:output-character character="&#137;" string=""/>
		<xsl:output-character character="&#138;" string=""/>
		<xsl:output-character character="&#139;" string=""/>
		<xsl:output-character character="&#140;" string=""/>
		<xsl:output-character character="&#141;" string=""/>
		<xsl:output-character character="&#142;" string=""/>
		<xsl:output-character character="&#143;" string=""/>
		<xsl:output-character character="&#144;" string=""/>
		<xsl:output-character character="&#145;" string=""/>
		<xsl:output-character character="&#146;" string=""/>
		<xsl:output-character character="&#147;" string=""/>
		<xsl:output-character character="&#148;" string=""/>
		<xsl:output-character character="&#149;" string=""/>
		<xsl:output-character character="&#150;" string=""/>
		<xsl:output-character character="&#151;" string=""/>
		<xsl:output-character character="&#152;" string=""/>
		<xsl:output-character character="&#153;" string=""/>
		<xsl:output-character character="&#154;" string=""/>
		<xsl:output-character character="&#155;" string=""/>
		<xsl:output-character character="&#156;" string=""/>
		<xsl:output-character character="&#157;" string=""/>
		<xsl:output-character character="&#158;" string=""/>
		<xsl:output-character character="&#159;" string=""/>
	</xsl:character-map>
	<xsl:output version="4.0" method="html" indent="no" encoding="UTF-8" doctype-public="-//W3C//DTD HTML 4.01 Transitional//EN" doctype-system="http://www.w3.org/TR/html4/loose.dtd" use-character-maps="myMap"/>
	<xsl:param name="SV_OutputFormat" select="'HTML'"/>
	<xsl:decimal-format name="tryFormat" grouping-separator="." decimal-separator=","/>
	<xsl:variable name="XML" select="/"/>
	<xsl:decimal-format name="format1" grouping-separator="." decimal-separator=","/>
	<xsl:template match="/">
		<html>
			<head>
				<meta http-equiv="X-UA-Compatible" content="IE=7"/>
			</head>
			<body style="font-family:Arial Narrow; ">
				<xsl:for-each select="$XML">
					<div style="border-color:#969696; border-style:solid; border-width:5px; padding:5px; ">
						<div style="border-color:#1e1e1e; border-style:solid; border-width:10px; padding:20px; ">
							<center>
								<img align="middle" alt="E-Fatura Logo" src="data:image/jpeg;base64,/9j/4AAQSkZJRgABAQAAAQABAAD/4QBoRXhpZgAASUkqAAgAAAADABIBAwABAAAAAQAAADEBAgAQ AAAAMgAAAGmHBAABAAAAQgAAAAAAAABTaG90d2VsbCAwLjIyLjAAAgACoAkAAQAAAKYBAAADoAkA AQAAAKYBAAAAAAAA/+EJ9Gh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8APD94cGFja2V0IGJl Z2luPSLvu78iIGlkPSJXNU0wTXBDZWhpSHpyZVN6TlRjemtjOWQiPz4gPHg6eG1wbWV0YSB4bWxu czp4PSJhZG9iZTpuczptZXRhLyIgeDp4bXB0az0iWE1QIENvcmUgNC40LjAtRXhpdjIiPiA8cmRm OlJERiB4bWxuczpyZGY9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkvMDIvMjItcmRmLXN5bnRheC1u cyMiPiA8cmRmOkRlc2NyaXB0aW9uIHJkZjphYm91dD0iIiB4bWxuczpleGlmPSJodHRwOi8vbnMu YWRvYmUuY29tL2V4aWYvMS4wLyIgeG1sbnM6dGlmZj0iaHR0cDovL25zLmFkb2JlLmNvbS90aWZm LzEuMC8iIGV4aWY6UGl4ZWxYRGltZW5zaW9uPSI0MjIiIGV4aWY6UGl4ZWxZRGltZW5zaW9uPSI0 MjIiIHRpZmY6SW1hZ2VXaWR0aD0iNDIyIiB0aWZmOkltYWdlSGVpZ2h0PSI0MjIiIHRpZmY6T3Jp ZW50YXRpb249IjEiLz4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gICAgICAgICAgICAgICAgICAg ICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAg ICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAg ICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAg ICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAg ICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAg ICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAg ICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAg ICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAg ICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAg ICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAg ICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAg ICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAg ICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAg ICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAg ICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAg ICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAg ICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAg ICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAg ICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAg ICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAg ICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAg ICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAg ICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAg ICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAg ICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAg ICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAg ICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAg ICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAg ICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAg ICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAg ICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAg ICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAg ICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAg ICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAg ICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAg ICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICA8P3hwYWNrZXQgZW5kPSJ3Ij8+/9sA QwADAgIDAgIDAwMDBAMDBAUIBQUEBAUKBwcGCAwKDAwLCgsLDQ4SEA0OEQ4LCxAWEBETFBUVFQwP FxgWFBgSFBUU/9sAQwEDBAQFBAUJBQUJFA0LDRQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQU FBQUFBQUFBQUFBQUFBQUFBQUFBQU/8AAEQgAaQBpAwEiAAIRAQMRAf/EAB8AAAEFAQEBAQEBAAAA AAAAAAABAgMEBQYHCAkKC//EALUQAAIBAwMCBAMFBQQEAAABfQECAwAEEQUSITFBBhNRYQcicRQy gZGhCCNCscEVUtHwJDNicoIJChYXGBkaJSYnKCkqNDU2Nzg5OkNERUZHSElKU1RVVldYWVpjZGVm Z2hpanN0dXZ3eHl6g4SFhoeIiYqSk5SVlpeYmZqio6Slpqeoqaqys7S1tre4ubrCw8TFxsfIycrS 09TV1tfY2drh4uPk5ebn6Onq8fLz9PX29/j5+v/EAB8BAAMBAQEBAQEBAQEAAAAAAAABAgMEBQYH CAkKC//EALURAAIBAgQEAwQHBQQEAAECdwABAgMRBAUhMQYSQVEHYXETIjKBCBRCkaGxwQkjM1Lw FWJy0QoWJDThJfEXGBkaJicoKSo1Njc4OTpDREVGR0hJSlNUVVZXWFlaY2RlZmdoaWpzdHV2d3h5 eoKDhIWGh4iJipKTlJWWl5iZmqKjpKWmp6ipqrKztLW2t7i5usLDxMXGx8jJytLT1NXW19jZ2uLj 5OXm5+jp6vLz9PX29/j5+v/aAAwDAQACEQMRAD8A/VOiioL6+ttMsp7y8njtbSBGlmnmcIkaKMsz MeAABkk0bgT1458QP2nfDvhbxDJ4W8N2F/8AEHxsvB0Hw6gla3PTNzMf3cC567jkelcJqHjHxT+1 FJeL4Z1a48B/Bq03i88Vg+Tfa0qZ8wWpb/UwDBzMeTjj+IVTl+JHhz4QeArPT/gf4dtJ7SG/FtqE j6dcuVLQmSGaX7ssiT4wtyPMU/wiQkLXuUcCoO1Vc0/5dkv8T6P+6tel09DzqmIurwdl36v0X6/m dDdaJ8c/HdpJfeJ/GWh/B7QgNz2OhwpfXqIf4ZbubEaN/tRrisTSv2evhJ4v8XXnhrxD4w8W/EHx Daq7Twa9r94UOzZ5gTyzHG2wyR7lTOzeoYDIr1P4l/CeL41aDod415eeGNUjETuypuZ7dmjkmtJo yQGB2Lz1VlBHcHW0D4L+GfDPxC1Xxlp0E9vq2pl3uFWUiFncIHfb3J8tepIB3FQCzZFjeSD5Zckt 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"/>
							</center>
							<h1 style="font-size:20px; text-align:center; ">
								<span style="font-family:Arial Narrow; ">
									<xsl:text>E-DEFTER BERATI</xsl:text>
								</span>
							</h1>
							<div style="float:none; font-size:10pt; margin-bottom:50px; overflow:hidden; ">
								<div style="float:left; overflow:hidden; width:49%; ">
									<div>
										<xsl:call-template name="Başlık">
											<xsl:with-param name="header" select="&apos;MÜKELLEF BİLGİLERİ&apos;"/>
										</xsl:call-template>
										<div>
											<table border="0" cellpadding="5" cellspacing="0" width="100%">
												<tbody>
													<xsl:variable name="UNVAN" select="//xbrli:xbrl/gl-cor:accountingEntries/gl-cor:entityInformation/gl-bus:organizationIdentifiers[gl-bus:organizationDescription=&quot;Kurum Unvanı&quot;]"/>
													<xsl:variable name="ADI_SOYADI" select="//xbrli:xbrl/gl-cor:accountingEntries/gl-cor:entityInformation/gl-bus:organizationIdentifiers[gl-bus:organizationDescription=&quot;Adı Soyadı&quot;]"/>
													<xsl:variable name="SUBE_ADI" select="//xbrli:xbrl/gl-cor:accountingEntries/gl-cor:entityInformation/gl-bus:organizationIdentifiers[gl-bus:organizationDescription=&quot;Şube Adı&quot;]"/>
													<tr>
														<th style="text-align:right; width:200px; ">
															<span style="font-family:Arial Narrow; ">
																<xsl:if test="$UNVAN != '' ">
																	<xsl:text>VKN</xsl:text>
																</xsl:if>
																<xsl:if test="$ADI_SOYADI != '' ">
																	<xsl:text>TCKN</xsl:text>
																</xsl:if>
															</span>
														</th>
														<th style="text-align:center; " align="center">
															<span style="font-family:Arial Narrow; ">
																<xsl:text>:</xsl:text>
															</span>
														</th>
														<td>
															<span>
																<xsl:for-each select="//xbrli:xbrl/xbrli:context/xbrli:entity/xbrli:identifier[@scheme=&quot;http://www.gib.gov.tr&quot;]">
																	<xsl:apply-templates/>
																</xsl:for-each>
															</span>
														</td>
													</tr>
													<tr>
														<th style="overflow:hidden; text-align:right; width:200px; ">
															<span style="font-family:Arial Narrow; ">
																<xsl:if test="$UNVAN != '' ">
																	<xsl:text>UNVAN</xsl:text>
																</xsl:if>
																<xsl:if test="$ADI_SOYADI != '' ">
																	<xsl:text>ADI SOYADI</xsl:text>
																</xsl:if>
																<xsl:if test="$SUBE_ADI != '' ">
																	<xsl:text>/ ŞUBE</xsl:text>
																</xsl:if>
															</span>
														</th>
														<th style="overflow:hidden; text-align:center; " align="center">
															<span style="font-family:Arial Narrow; ">
																<xsl:text>:</xsl:text>
															</span>
														</th>
														<td style="overflow:hidden; ">
															<xsl:if test="$UNVAN != '' ">
																<xsl:for-each select="//xbrli:xbrl/gl-cor:accountingEntries/gl-cor:entityInformation/gl-bus:organizationIdentifiers[gl-bus:organizationDescription=&quot;Kurum Unvanı&quot;]">
																	<xsl:value-of select="gl-bus:organizationIdentifier"/>
																</xsl:for-each>
															</xsl:if>
															<xsl:if test="$ADI_SOYADI != '' ">
																<xsl:for-each select="//xbrli:xbrl/gl-cor:accountingEntries/gl-cor:entityInformation/gl-bus:organizationIdentifiers[gl-bus:organizationDescription=&quot;Adı Soyadı&quot;]">
																	<xsl:value-of select="gl-bus:organizationIdentifier"/>
																</xsl:for-each>
															</xsl:if>
															<xsl:if test="$SUBE_ADI != '' ">
															 / 
																<xsl:for-each select="//xbrli:xbrl/gl-cor:accountingEntries/gl-cor:entityInformation/gl-bus:organizationIdentifiers[gl-bus:organizationDescription=&quot;Şube Adı&quot;]">
																	<xsl:value-of select="gl-bus:organizationIdentifier"/>
																</xsl:for-each>
															</xsl:if>
														</td>
													</tr>
													<tr>
														<th style="overflow:hidden; text-align:right; width:200px; ">
															<span style="font-family:Arial Narrow; ">
																<xsl:text>TELEFON</xsl:text>
															</span>
														</th>
														<th style="overflow:hidden; text-align:center; " align="center">
															<span style="font-family:Arial Narrow; ">
																<xsl:text>:</xsl:text>
															</span>
														</th>
														<td style="overflow:hidden; ">
															<span>
																<xsl:value-of select="//gl-cor:entityInformation/gl-bus:entityPhoneNumber/gl-bus:phoneNumber"/>
															</span>
														</td>
													</tr>
													<tr>
														<th style="overflow:hidden; text-align:right; width:200px; ">
															<span style="font-family:Arial Narrow; ">
																<xsl:text>FAX</xsl:text>
															</span>
														</th>
														<th style="overflow:hidden; text-align:center; " align="center">
															<span style="font-family:Arial Narrow; ">
																<xsl:text>:</xsl:text>
															</span>
														</th>
														<td style="overflow:hidden; ">
															<span>
																<xsl:value-of select="//gl-cor:entityInformation/gl-bus:entityFaxNumberStructure/gl-bus:entityFaxNumber"/>
															</span>
														</td>
													</tr>
													<tr>
														<th style="overflow:hidden; text-align:right; width:200px; ">
															<span style="font-family:Arial Narrow; ">
																<xsl:text>E-POSTA</xsl:text>
															</span>
														</th>
														<th style="overflow:hidden; text-align:center; " align="center">
															<span style="font-family:Arial Narrow; ">
																<xsl:text>:</xsl:text>
															</span>
														</th>
														<td style="overflow:hidden; ">
															<span>
																<xsl:value-of select="//gl-bus:entityEmailAddress"/>
															</span>
														</td>
													</tr>
												</tbody>
											</table>
										</div>
									</div>
								</div>
								<div style="float:right; overflow:hidden; width:49%; ">
									<div>
										<xsl:call-template name="Başlık">
											<xsl:with-param name="header" select="&apos;MESLEK MENSUBU BİLGİLERİ&apos;"/>
										</xsl:call-template>
										<div>
											<table border="0" cellpadding="5" cellspacing="0" width="100%">
												<tbody>
													<tr>
														<xsl:for-each select="/edefter:berat/xbrli:xbrl/gl-cor:accountingEntries/gl-cor:entityInformation/gl-bus:accountantInformation">
															<th style="overflow:hidden; text-align:right; ">
																<span style="font-family:Arial Narrow; ">
																	<xsl:text>UNVAN</xsl:text>
																</span>
															</th>
															<th style="overflow:hidden; text-align:center; " align="center">
																<span style="font-family:Arial Narrow; ">
																	<xsl:text>:</xsl:text>
																</span>
															</th>
															<td style="overflow:hidden; ">
																<span>
																	<xsl:value-of select="gl-bus:accountantName"/>
																</span>
															</td>
														</xsl:for-each>
													</tr>
													<tr>
														<xsl:for-each select="/edefter:berat/xbrli:xbrl/gl-cor:accountingEntries/gl-cor:entityInformation/gl-bus:accountantInformation">
															<th style="overflow:hidden; text-align:right; ">
																<span style="font-family:Arial Narrow; ">
																	<xsl:text>TELEFON</xsl:text>
																</span>
															</th>
															<th style="overflow:hidden; text-align:center; " align="center">
																<span style="font-family:Arial Narrow; ">
																	<xsl:text>:</xsl:text>
																</span>
															</th>
															<td style="overflow:hidden; ">
																<span>
																	<xsl:value-of select="gl-bus:accountantContactInformation/gl-bus:accountantContactPhone/gl-bus:accountantContactPhoneNumber"/>
																</span>
															</td>
														</xsl:for-each>
													</tr>
													<tr>
														<xsl:for-each select="/edefter:berat/xbrli:xbrl/gl-cor:accountingEntries/gl-cor:entityInformation/gl-bus:accountantInformation">
															<th style="overflow:hidden; text-align:right; ">
																<span style="font-family:Arial Narrow; ">
																	<xsl:text>FAX</xsl:text>
																</span>
															</th>
															<th style="overflow:hidden; text-align:center; " align="center">
																<span style="font-family:Arial Narrow; ">
																	<xsl:text>:</xsl:text>
																</span>
															</th>
															<td style="overflow:hidden; ">
																<span>
																	<xsl:value-of select="gl-bus:accountantContactInformation/gl-bus:accountantContactFax/gl-bus:accountantContactFaxNumber"/>
																</span>
															</td>
														</xsl:for-each>
													</tr>
													<tr>
														<xsl:for-each select="/edefter:berat/xbrli:xbrl/gl-cor:accountingEntries/gl-cor:entityInformation/gl-bus:accountantInformation">
															<th style="overflow:hidden; text-align:right; ">
																<span style="font-family:Arial Narrow; ">
																	<xsl:text>E-POSTA</xsl:text>
																</span>
															</th>
															<th style="overflow:hidden; text-align:center; " align="center">
																<span style="font-family:Arial Narrow; ">
																	<xsl:text>:</xsl:text>
																</span>
															</th>
															<td style="overflow:hidden; ">
																<span>
																	<xsl:value-of select="gl-bus:accountantContactInformation/gl-bus:accountantContactEmail/gl-bus:accountantContactEmailAddress"/>
																</span>
															</td>
														</xsl:for-each>
													</tr>
													<tr>
														<xsl:for-each select="/edefter:berat/xbrli:xbrl/gl-cor:accountingEntries/gl-cor:entityInformation/gl-bus:accountantInformation">
															<th style="overflow:hidden; text-align:right; ">
																<span style="font-family:Arial Narrow; ">
																	<xsl:text>SÖZLEŞME NO</xsl:text>
																</span>
															</th>
															<th style="overflow:hidden; text-align:center; " align="center">
																<span style="font-family:Arial Narrow; ">
																	<xsl:text>:</xsl:text>
																</span>
															</th>
															<td style="overflow:hidden; ">
																<span>
																	<xsl:value-of select="gl-bus:accountantEngagementTypeDescription"/>
																</span>
															</td>
														</xsl:for-each>
													</tr>
												</tbody>
											</table>
										</div>
									</div>
								</div>
							</div>
							<div style="font-size:10pt; margin-bottom:50px; ">
								<xsl:call-template name="Başlık">
									<xsl:with-param name="header" select="&apos;DOKÜMAN BİLGİLERİ&apos;"/>
								</xsl:call-template>
								<div>
									<table border="0" cellpadding="5" cellspacing="0" width="100%">
										<tbody>
											<tr>
												<td style="text-align:right; ">
													<span style="font-family:Arial Narrow; font-weight:bold; ">
														<xsl:text>DOKÜMAN TİPİ</xsl:text>
													</span>
												</td>
												<td style="text-align:center; ">
													<span style="font-family:Arial Narrow; ">
														<xsl:text>:</xsl:text>
													</span>
												</td>
												<xsl:for-each select="//gl-cor:accountingEntries">
													<xsl:for-each select="gl-cor:documentInfo">
														<td>
															<span>
																<xsl:choose>
																	<xsl:when test="gl-cor:entriesType='ledger'">
																		<xsl:text>Büyük Defter</xsl:text>
																	</xsl:when>
																	<xsl:when test="gl-cor:entriesType='journal'">
																		<xsl:text>Yevmiye Defteri</xsl:text>
																	</xsl:when>
																	<xsl:otherwise>
																		<xsl:text>Belge</xsl:text>
																	</xsl:otherwise>
																</xsl:choose>
															</span>
														</td>
													</xsl:for-each>
												</xsl:for-each>
												<td style="text-align:right; ">
													<span style="font-family:Arial Narrow; font-weight:bold; ">
														<xsl:text>OLUŞTURAN</xsl:text>
													</span>
												</td>
												<td style="text-align:center; ">
													<span style="font-family:Arial Narrow; ">
														<xsl:text>:</xsl:text>
													</span>
												</td>
												<xsl:for-each select="//gl-cor:accountingEntries">
													<xsl:for-each select="gl-cor:documentInfo">
														<td>
															<xsl:for-each select="gl-bus:creator">
																<xsl:apply-templates/>
															</xsl:for-each>
														</td>
													</xsl:for-each>
												</xsl:for-each>
											</tr>
											<tr>
												<td style="text-align:right; ">
													<span style="font-family:Arial Narrow; font-weight:bold; ">
														<xsl:text>DÖNEMİ</xsl:text>
													</span>
												</td>
												<td style="text-align:center; ">
													<span style="font-family:Arial Narrow; ">
														<xsl:text>:</xsl:text>
													</span>
												</td>
												<xsl:for-each select="//gl-cor:accountingEntries">
													<xsl:for-each select="gl-cor:documentInfo">
														<td>
															<xsl:for-each select="gl-cor:periodCoveredStart">
																<span>
																	<xsl:variable name="basDonem">
																		<xsl:value-of select="format-number(number(substring(string(string(.)), 9, 2)), '00', 'format1')"/>
																		<xsl:text> / </xsl:text>
																		<xsl:value-of select="format-number(number(substring(string(string(.)), 6, 2)), '00', 'format1')"/>
																		<xsl:text> / </xsl:text>
																		<xsl:value-of select="format-number(number(substring(string(string(string(.))), 1, 4)), '0000', 'format1')"/>
																	</xsl:variable>
																	<xsl:value-of select="$basDonem"/>
																</span>
															</xsl:for-each>
															<span style="font-family:Arial Narrow; ">
																<xsl:text> - </xsl:text>
															</span>
															<xsl:for-each select="gl-cor:periodCoveredEnd">
																<span>
																	<xsl:variable name="sonDonem">
																		<xsl:value-of select="format-number(number(substring(string(string(.)), 9, 2)), '00', 'format1')"/>
																		<xsl:text> / </xsl:text>
																		<xsl:value-of select="format-number(number(substring(string(string(.)), 6, 2)), '00', 'format1')"/>
																		<xsl:text> / </xsl:text>
																		<xsl:value-of select="format-number(number(substring(string(string(string(.))), 1, 4)), '0000', 'format1')"/>
																	</xsl:variable>
																	<xsl:value-of select="$sonDonem"/>
																</span>
															</xsl:for-each>
														</td>
													</xsl:for-each>
												</xsl:for-each>
												<td style="text-align:right; ">
													<span style="font-family:Arial Narrow; font-weight:bold; ">
														<xsl:text>TEKİL NO</xsl:text>
													</span>
												</td>
												<td style="text-align:center; ">
													<span style="font-family:Arial Narrow; ">
														<xsl:text>:</xsl:text>
													</span>
												</td>
												<xsl:for-each select="//gl-cor:accountingEntries">
													<xsl:for-each select="gl-cor:documentInfo">
														<td>
															<xsl:for-each select="gl-cor:uniqueID">
																<xsl:apply-templates/>
															</xsl:for-each>
														</td>
													</xsl:for-each>
												</xsl:for-each>
											</tr>
											<tr>
												<td style="text-align:right; ">
													<span style="font-family:Arial Narrow; font-weight:bold; ">
														<xsl:text>OLUŞTURMA TARİHİ</xsl:text>
													</span>
												</td>
												<td style="text-align:center; ">
													<span style="font-family:Arial Narrow; ">
														<xsl:text>:</xsl:text>
													</span>
												</td>
												<xsl:for-each select="//gl-cor:accountingEntries">
													<xsl:for-each select="gl-cor:documentInfo">
														<td>
															<xsl:for-each select="gl-cor:creationDate">
																<span>
																	<xsl:variable name="olusturmaTarih">
																		<xsl:value-of select="format-number(number(substring(string(string(.)), 9, 2)), '00', 'format1')"/>
																		<xsl:text> / </xsl:text>
																		<xsl:value-of select="format-number(number(substring(string(string(.)), 6, 2)), '00', 'format1')"/>
																		<xsl:text> / </xsl:text>
																		<xsl:value-of select="format-number(number(substring(string(string(string(.))), 1, 4)), '0000', 'format1')"/>
																	</xsl:variable>
																	<xsl:value-of select="$olusturmaTarih"/>
																</span>
															</xsl:for-each>
														</td>
													</xsl:for-each>
												</xsl:for-each>
												<td style="text-align:right; ">
													<span style="font-family:Arial Narrow; font-weight:bold; ">
														<xsl:text>KAYNAK UYGULAMA</xsl:text>
													</span>
												</td>
												<td style="text-align:center; ">
													<span style="font-family:Arial Narrow; ">
														<xsl:text>:</xsl:text>
													</span>
												</td>
												<xsl:for-each select="//gl-cor:accountingEntries">
													<xsl:for-each select="gl-cor:documentInfo">
														<td>
															<xsl:for-each select="gl-bus:sourceApplication">
																<xsl:apply-templates/>
															</xsl:for-each>
														</td>
													</xsl:for-each>
												</xsl:for-each>
											</tr>
											<tr>
												<td style="text-align:right; ">
													<span style="font-family:Arial Narrow; font-weight:bold; ">
														<xsl:text>HESAP DÖNEMİ</xsl:text>
													</span>
												</td>
												<td style="text-align:center; ">
													<span style="font-family:Arial Narrow; ">
														<xsl:text>:</xsl:text>
													</span>
												</td>
												<xsl:for-each select="//gl-cor:accountingEntries">
													<xsl:for-each select="gl-cor:entityInformation">
														<td>
															<xsl:for-each select="gl-bus:fiscalYearStart">
																<span>
																	<xsl:variable name="basDonem">
																		<xsl:value-of select="format-number(number(substring(string(string(.)), 9, 2)), '00', 'format1')"/>
																		<xsl:text> / </xsl:text>
																		<xsl:value-of select="format-number(number(substring(string(string(.)), 6, 2)), '00', 'format1')"/>
																		<xsl:text> / </xsl:text>
																		<xsl:value-of select="format-number(number(substring(string(string(string(.))), 1, 4)), '0000', 'format1')"/>
																	</xsl:variable>
																	<xsl:value-of select="$basDonem"/>
																</span>
															</xsl:for-each>
															<span style="font-family:Arial Narrow; ">
																<xsl:text> - </xsl:text>
															</span>
															<xsl:for-each select="gl-bus:fiscalYearEnd">
																<span>
																	<xsl:variable name="sonDonem">
																		<xsl:value-of select="format-number(number(substring(string(string(.)), 9, 2)), '00', 'format1')"/>
																		<xsl:text> / </xsl:text>
																		<xsl:value-of select="format-number(number(substring(string(string(.)), 6, 2)), '00', 'format1')"/>
																		<xsl:text> / </xsl:text>
																		<xsl:value-of select="format-number(number(substring(string(string(string(.))), 1, 4)), '0000', 'format1')"/>
																	</xsl:variable>
																	<xsl:value-of select="$sonDonem"/>
																</span>
															</xsl:for-each>
														</td>
													</xsl:for-each>
												</xsl:for-each>
												<td style="text-align:right; ">
													<span style="font-family:Arial Narrow; font-weight:bold; ">
														<xsl:text>ETTN</xsl:text>
													</span>
												</td>
												<td style="text-align:center; ">
													<span style="font-family:Arial Narrow; ">
														<xsl:text>:</xsl:text>
													</span>
												</td>
												<td colspan="1">
													<xsl:value-of select="//xbrli:context/xbrli:entity/xbrli:segment/gl-cor:uniqueID"/>
												</td>
											</tr>
											<tr>
												<td style="text-align:right; ">
													<span style="font-family:Arial Narrow; font-weight:bold; ">
														<xsl:text>AÇIKLAMA</xsl:text>
													</span>
												</td>
												<td style="text-align:center; ">
													<span style="font-family:Arial Narrow; ">
														<xsl:text>:</xsl:text>
													</span>
												</td>
												<xsl:for-each select="//gl-cor:accountingEntries">
													<xsl:for-each select="gl-cor:documentInfo">
														<td colspan="1">
															<xsl:for-each select="gl-cor:entriesComment">
																<xsl:apply-templates/>
															</xsl:for-each>
														</td>
													</xsl:for-each>
												</xsl:for-each>
												<xsl:for-each select="//gl-cor:accountingEntries">
													<xsl:for-each select="gl-cor:documentInfo"/>
												</xsl:for-each>
												<xsl:if test="//xbrli:context/xbrli:entity/xbrli:segment/gl-bus:measurableQuantity">
													<td style="text-align:right; ">
														<span style="font-family:Arial Narrow; font-weight:bold; ">
															<xsl:text>İLGİLİ DEFTER BOYUTU</xsl:text>
														</span>
													</td>
													<td style="text-align:center; ">
														<span style="font-family:Arial Narrow; ">
															<xsl:text>:</xsl:text>
														</span>
													</td>
													<td colspan="4">
														<xsl:value-of select="//xbrli:context/xbrli:entity/xbrli:segment/gl-bus:measurableQuantity"/> MB
													</td>
												</xsl:if>
											</tr>
											<xsl:if test="//xbrli:context/xbrli:entity/xbrli:segment/gl-bus:numberOfEntries">
												<tr>
													<td style="text-align:right; ">
														<span style="font-family:Arial Narrow; font-weight:bold; ">
															<xsl:text>YEVMİYE MADDESİ SAYISI</xsl:text>
														</span>
													</td>
													<td style="text-align:center; ">
														<span style="font-family:Arial Narrow; ">
															<xsl:text>:</xsl:text>
														</span>
													</td>
													<td colspan="4">
														<xsl:value-of select="//xbrli:context/xbrli:entity/xbrli:segment/gl-bus:numberOfEntries"/>
													</td>
												</tr>
											</xsl:if>
										</tbody>
									</table>
								</div>
							</div>
							<p>
								<xsl:if test="/edefter:berat/xbrli:xbrl/gl-cor:accountingEntries/gl-cor:entryHeader">
									<div style="font-size:8pt; margin-bottom:1px; margin-top:10px; padding:5px; text-align:center; ">
										<div style="font-size:8pt; margin-bottom:1px; margin-top:10px; padding:5px; text-align:center; ">
											<xsl:call-template name="Başlık">
												<xsl:with-param name="header" select="&quot;VERGİ DETAYI&quot;"/>
											</xsl:call-template>
										</div>
										<table align="center" style="font-size:12pt">
											<tbody>
												<tr>
													<td rowspan="2" style="border:solid #000000 1px; padding: 5px;">
														<b>HESAP KODU </b>
													</td>
													<td rowspan="2" style="border:solid #000000 1px; padding: 5px;">
														<b>HESAP ADI </b>
													</td>
													<td colspan="2" style="border:solid #000000 1px; padding: 5px;">
														<b>DÖNEM İÇİ DEĞİŞİKLİKLER</b>
													</td>
												</tr>
												<tr>
													<td style="border:solid #000000 1px; padding: 5px;">
														<b> BORÇ </b>
													</td>
													<td style="border:solid #000000 1px; padding: 5px;">
														<b> ALACAK</b>
													</td>
												</tr>
												<xsl:for-each select="/edefter:berat/xbrli:xbrl/gl-cor:accountingEntries/gl-cor:entryHeader">
													<xsl:for-each select="gl-cor:entryDetail[gl-cor:xbrlInfo/gl-cor:xbrlInclude='period_change' and gl-cor:debitCreditCode='D']">
														<tr>
															<xsl:variable name="hesapKodu" select="gl-cor:account/gl-cor:accountMainID"/>
															<td style="border:solid #000000 1px; padding: 5px;">
																<b>
																	<xsl:value-of select="gl-cor:account/gl-cor:accountMainID"/>
																</b>
															</td>
															<td style="border:solid #000000 1px; padding: 5px;">
																<b>
																	<xsl:value-of select="gl-cor:account/gl-cor:accountMainDescription"/>
																</b>
															</td>
															<td style="border:solid #000000 1px; padding: 5px;">
																<xsl:variable name="amountd" select="/edefter:berat/xbrli:xbrl/gl-cor:accountingEntries/gl-cor:entryHeader/gl-cor:entryDetail[gl-cor:account/gl-cor:accountMainID=$hesapKodu and gl-cor:debitCreditCode='D']/gl-cor:amount"/>
																<xsl:if test="$amountd">
																	<xsl:value-of select="format-number(number($amountd), '###.##0,00', 'tryFormat')"/>
																</xsl:if>
																<xsl:if test="not ($amountd)">0,00</xsl:if>
															</td>
															<td style="border:solid #000000 1px; padding: 5px;">
																<xsl:variable name="amountc" select="/edefter:berat/xbrli:xbrl/gl-cor:accountingEntries/gl-cor:entryHeader/gl-cor:entryDetail[gl-cor:account/gl-cor:accountMainID=$hesapKodu and gl-cor:debitCreditCode='C']/gl-cor:amount"/>
																<xsl:if test="$amountc">
																	<xsl:value-of select="format-number(number($amountc), '###.##0,00', 'tryFormat')"/>
																</xsl:if>
																<xsl:if test="not ($amountc)">0,00</xsl:if>
															</td>
														</tr>
													</xsl:for-each>
												</xsl:for-each>
											</tbody>
										</table>
									</div>
								</xsl:if>
							</p>
							<div style="font-size:8pt; margin-bottom:1px; margin-top:10px; padding:5px; text-align:center; ">
								<xsl:call-template name="Başlık">
									<xsl:with-param name="header" select="&quot;BERAT&apos;A KONU OLAN DOKÜMANIN İMZA DEĞERİ&quot;"/>
								</xsl:call-template>
							</div>
							<p>
								<div style="border-color:#aca899; border-style:solid; border-width:1px; padding:5px; overflow:auto; word-wrap:break-word;">
									<xsl:for-each select="//edefter:berat/ds:SignatureValue">
										<xsl:apply-templates/>
									</xsl:for-each>
								</div>
							</p>
							<div style="font-size:8pt; margin-bottom:1px; margin-top:10px; padding:5px; text-align:center; ">
								<xsl:call-template name="Başlık">
									<xsl:with-param name="header" select="&quot;GİB ONAY BİLGİLERİ&quot;"/>
								</xsl:call-template>
							</div>
							<p>
								<div style="border-color:#aca899; border-style:solid; border-width:1px; padding:5px; overflow:auto; word-wrap:break-word;">
									<xsl:for-each select="//edefter:berat/ds:Signature/ds:Object/xades:QualifyingProperties/xades:UnsignedProperties/xades:UnsignedSignatureProperties/xades:CounterSignature/ds:Signature/ds:SignatureValue">
										<xsl:apply-templates/>
									</xsl:for-each>
									<xsl:for-each select="/edefter:berat/ds:Signature/ds:Object/xades:UnsignedProperties/xades:UnsignedSignatureProperties/xades:CounterSignature/ds:Signature/ds:SignatureValue">
										<xsl:apply-templates/>
									</xsl:for-each>
								</div>
							</p>
						</div>
					</div>
				</xsl:for-each>
			</body>
		</html>
	</xsl:template>
	<xsl:template name="Başlık">
		<xsl:param name="header" select="&apos;&apos;"/>
		<div>
			<hr/>
			<blockquote style="background-color:white; font-size:12pt; font-weight:bold; margin-left:auto; margin-right:auto; margin-top:-18px; padding-left:5px; padding-right:5px; text-align:center; width:300px; ">
				<span>
					<xsl:value-of select="$header"/>
				</span>
			</blockquote>
		</div>
	</xsl:template>
</xsl:stylesheet>
