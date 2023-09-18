<?

function agentImportProps()
{

	$Import = new ImportCrm;
	$Import->importProps();

	return "agentImportProps();";
}

function agentImportSections()
{

	$Import = new ImportCrm;
	$Import->importSections();

	return "agentImportSections();";
}

function agentImportElements()
{

	$Import = new ImportCrm;
	$result = $Import->importElements();

	// return "agentImportElements();";
}

function agentImportElementsPrice()
{

	$Import = new ImportCrm;
	$result = $Import->importElementsPrice();

	return "agentImportElementsPrice();";
}
