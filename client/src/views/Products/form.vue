<template>
	<main-content class="product-form" :breads="breads">
		<b-row>
			<b-col cols="12" class="pb-3">
				<b-form @submit.prevent="handleSave">
					<b-card header="Product details">
						<b-row cols="3">
							<b-col>
								<b-form-group label="Product Name" label-for="name">
									<b-form-input id="name" placeholder="Enter Product Name" v-model="product.name" />
									<!-- <input-error namespace="Product" field="name" /> -->
								</b-form-group>
							</b-col>
							<b-col>
								<b-form-group label="Barcode Symbology" label-for="barcode_type">
									<b-select id="barcode_type" v-model="product.barcode_type" :options="barcodeOpt" @change="generateCode" />
									<!-- <input-error namespace="Product" field="barcode_type" /> -->
								</b-form-group>
							</b-col>
							<b-col>
								<b-form-group label="Product Code" label-for="code">
									<b-input-group>
										<b-form-input id="code" placeholder="code" v-model="product.code" disabled />
										<b-input-group-prepend is-text>
											<span class="mb-0 c-pointer" @click="generateCode">
												<b-icon icon="upc"></b-icon>
											</span>
										</b-input-group-prepend>
									</b-input-group>
									<!-- <input-error namespace="Product" field="code" /> -->
								</b-form-group>
							</b-col>
							<b-col>
								<b-form-group label="Product Price" label-for="price">
									<b-form-input id="price" type="number" placeholder="Enter Product Price" v-model.number="product.price" />
									<!-- <input-error namespace="Product" field="price" /> -->
								</b-form-group>
							</b-col>
							<b-col>
								<b-form-group label="Product Cost" label-for="cost">
									<b-form-input id="cost" type="number" placeholder="Enter Product Cost" v-model.number="product.cost" />
									<!-- <input-error namespace="Product" field="cost" /> -->
								</b-form-group>
							</b-col>
							<b-col>
								<b-form-group label="Product Minimum Alert" label-for="minimum">
									<b-form-input id="minimum" type="number" placeholder="Enter Product Minimum Alert" v-model.number="product.minimum" />
									<!-- <input-error namespace="Product" field="minimum" /> -->
								</b-form-group>
							</b-col>
							<b-col>
								<b-form-group label="Product Order Tax" label-for="tax">
									<b-input-group append="%">
										<b-form-input id="tax" placeholder="tax" v-model.number="product.tax" />
									</b-input-group>
								</b-form-group>
							</b-col>
							<b-col>
								<b-form-group label="Tax Type" label-for="tax_method">
									<b-select id="tax_method" v-model.number="product.tax_method" :options="taxMethodOpt" />
									<!-- <input-error namespace="Product" field="tax_method" /> -->
								</b-form-group>
							</b-col>
							<b-col>
								<b-form-group label="Category" label-for="category_id">
									<b-select id="category_id" v-model.number="product.category_id" :options="categoriesOpt" />
									<!-- <input-error namespace="Product" field="category_id" /> -->
								</b-form-group>
							</b-col>
							<b-col>
								<b-form-group label="Brand" label-for="brand">
									<b-select id="brand" v-model.number="product.brand_id" :options="brandsOpt" />
									<!-- <input-error namespace="Product" field="brand" /> -->
								</b-form-group>
							</b-col>
							<b-col>
								<b-form-group label="Product Unit" label-for="unit_id">
									<b-select id="unit_id" v-model.number="product.unit_id" :options="unitsOpt" @change="setSubUnits" />
									<!-- <input-error namespace="Product" field="unit_id" /> -->
								</b-form-group>
							</b-col>
							<b-col>
								<b-form-group label="Purchase Unit" label-for="purchase_unit">
									<b-select id="purchase_unit" v-model.number="product.purchase_unit_id" :options="purchaseUnitsOpt" />
									<!-- <input-error namespace="Product" field="purchase_unit" /> -->
								</b-form-group>
							</b-col>
							<b-col>
								<b-form-group label="Sale Unit" label-for="sale_unit">
									<b-select id="sale_unit" v-model.number="product.sale_unit_id" :options="saleUnitsOpt" />
									<!-- <input-error namespace="Product" field="sale_unit" /> -->
								</b-form-group>
							</b-col>
						</b-row>
					</b-card>
				</b-form>
			</b-col>
			<b-col cols="6" class="py-3">
				<b-card header="Upload product images">
					<VueUploadMultipleImage
						class="d-flex flex-column align-items-center"
						browseText="(or) Select"
						dragText="Drag & Drop Multiple images For product"
						primaryText="Success"
						markIsPrimaryText="Make it default"
						popupText="This image will be displayed as default"
						:data-images="images"
						@upload-success="uploadImageSuccess"
						@before-remove="beforeRemoveImage"
						@mark-is-primary="markIsPrimary"
						:showEdit="false"
						accept="image/jpeg,image/png,image/jpg"
					/>
				</b-card>
			</b-col>
			<b-col cols="6" class="py-3" v-if="!isUpdate">
				<b-card header="Product variants" class="h-100">
					<b-form-tags v-model="variants" no-outer-focus class="mb-2 border-0" tagVariant="success" :tag-validator="tagValidator">
						<template v-slot="{ tags, inputAttrs, inputHandlers, tagVariant, addTag, removeTag }">
							<b-input-group class="mb-2">
								<b-form-input v-bind="inputAttrs" v-on="inputHandlers" placeholder="Enter Variant Name" id="variantName"></b-form-input>
								<b-input-group-append>
									<b-button @click="addTag()" variant="outline-success">Add</b-button>
								</b-input-group-append>
							</b-input-group>
							<div id="tags-validation-help" class="small text-muted">Product variant must be 3 to 54 characters in length.</div>
							<div class="d-block font-default mt-1">
								<b-form-tag v-for="tag in tags" @remove="removeTag(tag)" :key="tag" :title="tag" :variant="tagVariant" class="mr-1">
									{{ tag }}
								</b-form-tag>
							</div>
						</template>
					</b-form-tags>
				</b-card>
			</b-col>
			<b-col cols="6" class="py-3">
				<b-card header="My image uploader">
					<image-uploader
						@upload-image="uploadImage"
						:data-images="imagesData"
						@delete-image="deleteImage"
						:max="10"
						:multiple="true"
						@error="handleUploadError"
					></image-uploader>
				</b-card>
			</b-col>
		</b-row>
		<b-btn :variant="`${isUpdate ? 'success' : 'primary'}`" type="submit" @click="handleSave">save</b-btn>
	</main-content>
</template>

<script>
	import { mapActions, mapState } from "vuex";
	import DefaultInput from "@/components/ui/DefaultInput.vue";
	import VueTagsInput from "@johmun/vue-tags-input";
	import VueUploadMultipleImage from "vue-upload-multiple-image";
	import ImageUploader from "@/components/ImageUploader";
	export default {
		components: { DefaultInput, VueTagsInput, VueUploadMultipleImage, ImageUploader },

		data: () => ({
			product: {
				name: null,
				barcode_type: "CODE128",
				code: null,
				price: null,
				cost: null,
				minimum: 0,
				tax: 0,
				tax_method: 0,
				note: null,
				category_id: null,
				brand_id: null,
				unit_id: null,
				purchase_unit_id: null,
				sale_unit_id: null,
				has_variants: false,
				has_images: false
			},
			purchaseUnitsOpt: [{ text: "Choose Purchase Unit", value: null, disabled: true }],
			saleUnitsOpt: [{ text: "Choose Sale Unit", value: null, disabled: true }],
			barcodeOpt: [
				{ text: "Choose Symbology", value: null, disabled: true },
				{ text: "Code 128", value: "CODE128", codeLength: 8, disabled: false },
				{ text: "Code 39", value: "CODE39", codeLength: 8, disabled: false },
				{ text: "EAN8", value: "EAN-8", codeLength: 7, disabled: false },
				{ text: "EAN13", value: "EAN-13", codeLength: 12, disabled: false },
				{ text: "UPC", value: "UPC", codeLength: 11, disabled: false }
			],
			taxMethodOpt: [
				{ text: "Choose Symbology", value: null, disabled: true },
				{ text: "Inclusive", value: 1, disabled: false },
				{ text: "Exclusive", value: 0, disabled: false }
			],
			images: [
				{
					url: "https://picsum.photos/300/300/?image=10"
				},
				{
					url: "https://picsum.photos/300/300/?image=11"
				},
				{
					url: "https://picsum.photos/300/300/?image=12"
				},
				{
					url: "https://picsum.photos/300/300/?image=13"
				},
				{
					url: "https://picsum.photos/300/300/?image=14"
				},
				{
					url: "https://picsum.photos/300/300/?image=15"
				},
				{
					url: "https://picsum.photos/300/300/?image=16"
				},
				{
					url: "https://picsum.photos/300/300/?image=17"
				},
				{
					url: "https://picsum.photos/300/300/?image=18"
				},
				{
					url: "https://picsum.photos/300/300/?image=19"
				},
				{
					url: "https://media3.giphy.com/avatars/Packly/C4AvqGR2zXrN.gif"
				},
				{
					url: "https://picsum.photos/300/300/?image=20"
				},
				{
					url: "https://picsum.photos/300/300/?image=21"
				},
				{
					url: "https://picsum.photos/300/300/?image=22"
				},
				{
					url: "https://picsum.photos/300/300/?image=23"
				},
				{
					url: "https://images.unsplash.com/photo-1518791841217-8f162f1e1131?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=800&q=60"
				},
				{
					url: "https://images.unsplash.com/photo-1518791841217-8f162f1e1131?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=800&q=60"
				},
				{
					url: "https://images.unsplash.com/photo-1518791841217-8f162f1e1131?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=800&q=60"
				}
			],
			variants: [],
			formData: new FormData()
		}),

		async mounted() {
			this.generateCode();
			this.getCategoriesOpt();
			this.getBrandsOpt();
			this.getUnitsOpt();

			if (this.isUpdate) {
				await this.getProduct(this.$route.params.productId);

				this.product = { ...this.oldProduct };

				this.$nextTick(() => {
					this.setSubUnits(this.product.unit_id);

					this.images = this.product.images.map((img) => ({
						default: img.default,
						name: img.name,
						path: img.path
					}));
				});
			}
		},

		computed: {
			...mapState({
				categoriesOpt: (state) => state.Categories.options,
				brandsOpt: (state) => state.Brands.options,
				unitsOpt: (state) => state.Units.options,
				oldProduct: (state) => state.Products.one
			}),

			imagesData() {
				return this.images.map((image) => {
					image.deleted = false;
					return image;
				});
			},

			isUpdate() {
				return this.$route.params.productId;
			},

			breads() {
				let breads = [
					{ title: "Dashboard", link: "/" },
					{ title: "Products", link: "/products" }
				];

				if (this.isUpdate) {
					breads = [...breads, { title: this.oldProduct.name, link: `/products/${this.$route.params.productId}` }, { title: "Edit" }];
				} else {
					breads.push({ title: "Create" });
				}

				return breads;
			}
		},

		methods: {
			...mapActions({
				create: "Products/create",
				update: "Products/update",
				getCategoriesOpt: "Categories/options",
				getBrandsOpt: "Brands/options",
				getUnitsOpt: "Units/options",
				getProduct: "Products/edit"
			}),

			uploadImageSuccess(formData, _index, fileList) {
				this.images = fileList;
				this.formData = formData;
			},

			beforeRemoveImage(_index, done, fileList) {
				this.images = fileList;
				this.formData.forEach((f) => console.log(f));
				var r = confirm("remove image");

				if (r == true) return done();
			},

			uploadImage(image) {
				console.log(image);
			},

			handleUploadError(err) {
				console.log(err);
			},

			// deleteImage(_index, done, fileList) {
			// 	this.images = fileList;
			// 	this.formData.forEach((f) => console.log(f));
			// 	var r = confirm("delete image");

			// 	if (r == true) return done();
			// },

			deleteImage(image, stop) {
				var r = confirm("are you sure to delete image");

				if (!r) return stop();

				image.deleted = true;
			},

			markIsPrimary(_index, fileList) {
				this.images = fileList;
			},

			setSubUnits(v) {
				let mainUnit = this.unitsOpt.find((opt) => opt.value == v);

				this.purchaseUnitsOpt = [this.purchaseUnitsOpt[0], ...mainUnit.sub_units];

				this.saleUnitsOpt = [this.saleUnitsOpt[0], ...mainUnit.sub_units];
			},

			tagValidator(tag) {
				return tag.length > 2 && tag.length < 54;
			},

			generateCode() {
				this.product.barcode_type = this.product.barcode_type || this.barcodeOpt[1].value;

				let opt = this.barcodeOpt.find((opt) => opt.value == this.product.barcode_type);

				let length = opt.codeLength;

				let code = this.randomInteger(length);

				this.product.code = code.toString();
			},

			randomInteger(length) {
				let min, max;
				min = +(1 + Array(length).join("0"));
				max = +`${min}0`;
				return Math.floor(Math.random() * (max - min)) + min;
			},

			handleSave() {
				for (let k in this.product) {
					if (k !== "images" && k !== "variants") {
						this.formData.set(k, this.product[k]);
					}
				}

				if (this.images.length) {
					this.formData.append("has_images", true);

					this.images.forEach((img, i) => {
						this.formData.append(`images[${i}][path]`, img.path);
						this.formData.append(`images[${i}][name]`, img.name);
						this.formData.append(`images[${i}][default]`, img.default);
					});
				}

				if (this.isUpdate && this.variants.length) {
					this.formData.append("has_variants", true);

					this.variants.forEach((variant, i) => {
						this.formData.append(`variants[${i}][name]`, variant);
					});
				}

				try {
					if (this.isUpdate) return this.update(this.formData);

					this.create(this.formData);
				} catch (e) {
					console.log(e);
				}
			}
		}
	};
</script>

<style scoped lang="scss">
	.product-form {
		#variantName {
			&:focus,
			&.focus {
				border-color: lighten(#28a745, 25%);
			}
		}
	}
</style>
