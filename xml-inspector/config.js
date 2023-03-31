/**
 * Relative path from current directory to directory that contains
 * form definitions
 */
export const sourcePath = "./source/";

/**
 * View definitions to check in the basePath directory
 * @remarks
 * Paths must not start with ./ or /
 */
export const files = [
  "backstop/search.views.xml",
  "insect/manager/ento.views.xml",
  "insect/ento.views.xml",
  "insect/guest/ento.views.xml",
  "mammal/mammal.views.xml",
  "mammal/manager/mammal.views.xml",
  "mammal/guest/mammal.views.xml",
  "herpetology/manager/herpetology.views.xml",
  "herpetology/guest/herpetology.views.xml",
  "herpetology/herpetology.views.xml",
  "vascplant/manager/botany.views.xml",
  "vascplant/manager/vascplant.views.xml",
  "vascplant/vascplant.views.xml",
  "botany/botany.views.xml",
  "botany/manager/botany.views.xml",
  "botany/guest/botany.views.xml",
  "vertpaleo/manager/vertpaleo.views.xml",
  "vertpaleo/guest/vertpaleo.views.xml",
  "vertpaleo/vertpaleo.views.xml",
  "common/common.views.xml",
  "bird/manager/bird.views.xml",
  "bird/bird.views.xml",
  "bird/guest/bird.views.xml",
  "invertebrate/manager/invertebrate.views.xml",
  "invertebrate/invertebrate.views.xml",
  "invertebrate/guest/invertebrate.views.xml",
  "reptile/reptile.views.xml",
  "reptile/manager/reptile.views.xml",
  "invertpaleo/manager/paleo.views.xml",
  "invertpaleo/paleo.views.xml",
  "invertpaleo/guest/paleo.views.xml",
  "backstop/global.views.xml",
  "fish/manager/fish.views.xml",
  "fish/fish.views.xml",
  "fish/guest/fish.views.xml",
  "accessions/accessions.views.xml",

  /**
   * These system and meta-views are not going to be editable in form editor,
   * so are excluded
   */
  // "backstop/gbif.views.xml",
  // "backstop/system.views.xml",
  // "backstop/preferences.views.xml",
  // "backstop/editorpanel.views.xml",
  // "fish/fishbase.views.xml",
];
