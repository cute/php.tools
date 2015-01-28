<?php
/**
 * @codeCoverageIgnore
 */
final class CodeFormatter {
	private $passes = [
		'RTrim' => false,

		'LongArray' => false,
		'StripExtraCommaInArray' => false,
		'NoSpaceAfterPHPDocBlocks' => false,
		'RemoveUseLeadingSlash' => false,
		'OrderMethod' => false,
		'ShortArray' => false,
		'MergeElseIf' => false,
		'AutoPreincrement' => false,
		'MildAutoPreincrement' => false,

		'CakePHPStyle' => false,

		'StripNewlineAfterClassOpen' => false,
		'StripNewlineAfterCurlyOpen' => false,
		'EliminateDuplicatedEmptyLines' => false,
		'AlignEqualsByConsecutiveBlocks' => false,
		'SortUseNameSpace' => false,
		'NoneDocBlockMinorCleanUp' => false,
		'SpaceAroundExclamationMark' => false,
		'NoSpaceBetweenFunctionAndBracket' => false,
		'TightConcat' => false,
		'AllmanStyleBraces' => false,
		'NamespaceMergeWithOpenTag' => false,
		'MergeNamespaceWithOpenTag' => false,

		'LeftAlignComment' => false,

		'PSR2AlignObjOp' => false,
		'PSR2SingleEmptyLineAndStripClosingTag' => false,
		'PSR2ModifierVisibilityStaticOrder' => false,
		'PSR2CurlyOpenNextLine' => false,
		'PSR2LnAfterNamespace' => false,
		'PSR2IndentWithSpace' => false,
		'PSR2KeywordsLowerCase' => false,

		'PSR1MethodNames' => false,
		'PSR1ClassNames' => false,

		'PSR1ClassConstants' => false,
		'PSR1BOMMark' => false,
		'PSR1OpenTags' => false,

		'EliminateDuplicatedEmptyLines' => false,
		'Reindent' => false,
		'ReindentObjOps' => false,

		'AlignTypehint' => false,
		'AlignDoubleSlashComments' => false,
		'AlignDoubleArrow' => false,
		'AlignEquals' => false,

		'ReindentIfColonBlocks' => false,
		'ReindentLoopColonBlocks' => false,
		'ReindentColonBlocks' => false,
		'ResizeSpaces' => false,
		'YodaComparisons' => false,

		'MergeDoubleArrowAndArray' => false,
		'MergeCurlyCloseAndDoWhile' => false,
		'MergeParenCloseWithCurlyOpen' => false,
		'NormalizeLnAndLtrimLines' => false,
		'ExtraCommaInArray' => false,
		'SmartLnAfterCurlyOpen' => false,
		'AddMissingCurlyBraces' => false,
		'OrderUseClauses' => false,
		'AutoImportPass' => false,
		'ConstructorPass' => false,
		'SettersAndGettersPass' => false,
		'NormalizeIsNotEquals' => false,
		'RemoveIncludeParentheses' => false,
		'TwoCommandsInSameLine' => false,

		'SpaceBetweenMethods' => false,
		'GeneratePHPDoc' => false,
		'ReturnNull' => false,
		'AddMissingParentheses' => false,
		'WrongConstructorName' => false,
		'JoinToImplode' => false,
		'EncapsulateNamespaces' => false,
		'PrettyPrintDocBlocks' => false,
		'StrictBehavior' => false,
		'StrictComparison' => false,
	];

	public function __construct() {
		$this->passes['TwoCommandsInSameLine'] = new TwoCommandsInSameLine();
		$this->passes['RemoveIncludeParentheses'] = new RemoveIncludeParentheses();
		$this->passes['NormalizeIsNotEquals'] = new NormalizeIsNotEquals();
		$this->passes['OrderUseClauses'] = new OrderUseClauses();
		$this->passes['AddMissingCurlyBraces'] = new AddMissingCurlyBraces();
		$this->passes['ExtraCommaInArray'] = new ExtraCommaInArray();
		$this->passes['NormalizeLnAndLtrimLines'] = new NormalizeLnAndLtrimLines();
		$this->passes['MergeParenCloseWithCurlyOpen'] = new MergeParenCloseWithCurlyOpen();
		$this->passes['MergeCurlyCloseAndDoWhile'] = new MergeCurlyCloseAndDoWhile();
		$this->passes['MergeDoubleArrowAndArray'] = new MergeDoubleArrowAndArray();
		$this->passes['ResizeSpaces'] = new ResizeSpaces();
		$this->passes['ReindentColonBlocks'] = new ReindentColonBlocks();
		$this->passes['ReindentLoopColonBlocks'] = new ReindentLoopColonBlocks();
		$this->passes['ReindentIfColonBlocks'] = new ReindentIfColonBlocks();
		$this->passes['ReindentObjOps'] = new ReindentObjOps();
		$this->passes['Reindent'] = new Reindent();
		$this->passes['EliminateDuplicatedEmptyLines'] = new EliminateDuplicatedEmptyLines();
		$this->passes['LeftAlignComment'] = new LeftAlignComment();
		$this->passes['RTrim'] = new RTrim();
	}

	public function enablePass($pass) {
		$args = func_get_args();
		if (!isset($args[1])) {
			$this->passes[$pass] = new $pass();
		} else {
			$this->passes[$pass] = new $pass($args[1]);
		}
	}

	public function disablePass($pass) {
		$this->passes[$pass] = null;
	}

	public function getPassesNames() {
		return array_keys(array_filter($this->passes));
	}

	public function formatCode($source = '') {
		$passes = array_map(
			function ($pass) {
				return clone $pass;
			},
			array_filter($this->passes)
		);
		$foundTokens = [];
		$tkns = token_get_all($source);
		foreach ($tkns as $token) {
			list($id, $text) = $this->getToken($token);
			$foundTokens[$id] = $id;
		}
		while (($pass = array_pop($passes))) {
			if ($pass->candidate($source, $foundTokens)) {
				$source = $pass->format($source);
			}
		}
		return $source;
	}

	protected function getToken($token) {
		if (isset($token[1])) {
			return $token;
		} else {
			return [$token, $token];
		}
	}
}